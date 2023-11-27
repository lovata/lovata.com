<?php namespace Backend\Behaviors\ImportExportController;

use Str;
use Lang;
use Backend;
use Illuminate\Database\Eloquent\MassAssignmentException;
use ApplicationException;
use Exception;

/**
 * HasImportMode contains logic for imports
 */
trait HasImportMode
{
    /**
     * @var Model importModel
     */
    public $importModel;

    /**
     * @var array importColumns configuration.
     */
    public $importColumns;

    /**
     * @var Backend\Classes\WidgetBase importUploadFormWidget reference to the widget used for uploading import file.
     */
    protected $importUploadFormWidget;

    /**
     * @var Backend\Classes\WidgetBase importOptionsFormWidget reference to the widget used for specifying import options.
     */
    protected $importOptionsFormWidget;

    /**
     * import action
     */
    public function import()
    {
        if ($response = $this->checkPermissionsForType('import')) {
            return $response;
        }

        $this->addJs('js/october.import.js');
        $this->addCss('css/import.css');

        $this->controller->pageTitle = $this->controller->pageTitle
            ?: Lang::get($this->getConfig('import[title]', 'Import Records'));

        $this->prepareImportVars();
    }

    /**
     * onImport
     */
    public function onImport()
    {
        try {
            $model = $this->importGetModel();
            $matches = post('column_match', []);

            if ($optionData = post('ImportOptions')) {
                $model->fill($optionData);
            }

            $importOptions = $this->getFormatOptionsForModel();
            $importOptions['sessionKey'] = $this->importUploadFormWidget->getSessionKey();

            $model->file_format = $importOptions['fileFormat'] ?? 'json';
            $model->import($matches, $importOptions);

            $this->vars['importResults'] = $model->getResultStats();
            $this->vars['returnUrl'] = $this->getRedirectUrlForType('import');
        }
        catch (MassAssignmentException $ex) {
            $this->controller->handleError(new ApplicationException(Lang::get(
                'backend::lang.model.mass_assignment_failed',
                ['attribute' => $ex->getMessage()]
            )));
        }
        catch (Exception $ex) {
            $this->controller->handleError($ex);
        }

        $this->vars['sourceIndexOffset'] = $this->getImportSourceIndexOffset($importOptions['firstRowTitles']);

        return $this->importExportMakePartial('import_result_form');
    }

    /**
     * onImportLoadForm
     */
    public function onImportLoadForm()
    {
        try {
            if (!$this->isCustomFileFormat()) {
                $this->checkRequiredImportColumns();
            }
        }
        catch (Exception $ex) {
            $this->controller->handleError($ex);
        }

        return $this->importExportMakePartial('import_form');
    }

    /**
     * onImportLoadColumnSampleForm
     */
    public function onImportLoadColumnSampleForm()
    {
        if (($columnId = post('file_column_id', false)) === false) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.missing_column_id_error'));
        }

        $columns = $this->getImportFileColumns();
        if (!array_key_exists($columnId, $columns)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.unknown_column_error'));
        }

        $path = $this->getImportFilePath();

        if (!$fileFormat = post('file_format', 'json')) {
            return null;
        }

        if ($fileFormat === 'json') {
            $data = $this->getImportSampleColumnsFromJson($path, (int) $columnId);
        }
        else {
            $data = $this->getImportSampleColumnsFromCsv($path, (int) $columnId);
        }

        // Clean up data
        foreach ($data as $index => $sample) {
            $data[$index] = Str::limit($sample, 100);
            if (!strlen($data[$index])) {
                unset($data[$index]);
            }
        }

        $this->vars['columnName'] = array_get($columns, $columnId);
        $this->vars['columnData'] = $data;

        return $this->importExportMakePartial('column_sample_form');
    }

    /**
     * prepareImportVars for the view data.
     */
    public function prepareImportVars()
    {
        $this->vars['importUploadFormWidget'] = $this->importUploadFormWidget;
        $this->vars['importOptionsFormWidget'] = $this->importOptionsFormWidget;
        $this->vars['importDbColumns'] = $this->getImportDbColumns();
        $this->vars['importFileColumns'] = $this->getImportFileColumns();
        $this->vars['importCustomFormat'] = $this->isCustomFileFormat();

        // Make these variables available to widgets
        $this->controller->vars += $this->vars;
    }

    /**
     * importRender
     */
    public function importRender()
    {
        return $this->importExportMakePartial('container_import');
    }

    /**
     * importGetModel
     */
    public function importGetModel()
    {
        return $this->getModelForType('import');
    }

    /**
     * getImportDbColumns
     */
    protected function getImportDbColumns()
    {
        if ($this->importColumns !== null) {
            return $this->importColumns;
        }

        $columnConfig = $this->getConfig('import[list]');

        $columns = $this->makeListColumns($columnConfig, $this->importGetModel());

        $columns = $this->controller->importExportExtendColumns($columns, 'import');

        if (empty($columns)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.empty_import_columns_error'));
        }

        return $this->importColumns = $columns;
    }

    /**
     * getImportFileColumns
     */
    protected function getImportFileColumns()
    {
        if (!$path = $this->getImportFilePath()) {
            return null;
        }

        if (!$fileFormat = post('file_format', 'json')) {
            return null;
        }

        if ($fileFormat === 'json') {
            return $this->getImportFileColumnsFromJson($path);
        }
        else {
            return $this->getImportFileColumnsFromCsv($path);
        }
    }

    /**
     * getImportSourceIndexOffset to add to the reported row number in status messages
     *
     * @param bool $firstRowTitles Whether or not the first row contains column titles
     * @return int $offset
     */
    protected function getImportSourceIndexOffset($firstRowTitles)
    {
        return $firstRowTitles ? 2 : 1;
    }

    /**
     * makeImportUploadFormWidget
     */
    protected function makeImportUploadFormWidget()
    {
        if (!$this->getConfig('import')) {
            return null;
        }

        $widgetConfig = $this->makeConfig('~/modules/backend/behaviors/importexportcontroller/partials/fields_import.yaml');
        $widgetConfig->model = $this->importGetModel();
        $widgetConfig->alias = 'importUploadForm';
        $widgetConfig->useModelFields = false;

        $widget = $this->makeWidget(\Backend\Widgets\Form::class, $widgetConfig);

        // Set presets
        $widget->setFormValues($this->getFormatOptionsForPost());

        // Reset data on refresh
        $widget->bindEvent('form.beforeRefresh', function ($holder) {
            $holder->data = [];
        });

        return $widget;
    }

    /**
     * makeImportOptionsFormWidget
     */
    protected function makeImportOptionsFormWidget()
    {
        $widget = $this->makeOptionsFormWidgetForType('import');

        if (!$widget && $this->importUploadFormWidget) {
            $stepSection = $this->importUploadFormWidget->getField('step3_section');
            $stepSection->hidden = true;
        }

        return $widget;
    }

    /**
     * getImportFilePath
     */
    protected function getImportFilePath()
    {
        return $this
            ->importGetModel()
            ->getImportFilePath($this->importUploadFormWidget->getSessionKey());
    }

    /**
     * importIsColumnRequired
     */
    public function importIsColumnRequired($columnName)
    {
        $model = $this->importGetModel();

        return $model->isAttributeRequired($columnName);
    }

    /**
     * checkRequiredImportColumns
     */
    protected function checkRequiredImportColumns()
    {
        if (!$matches = post('column_match', [])) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.match_some_column_error'));
        }

        $dbColumns = $this->getImportDbColumns();
        foreach ($dbColumns as $column => $label) {
            if (!$this->importIsColumnRequired($column)) {
                continue;
            }

            $found = false;
            foreach ($matches as $matchedColumns) {
                if (in_array($column, $matchedColumns)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new ApplicationException(Lang::get('backend::lang.import_export.required_match_column_error', [
                    'label' => Lang::get($label)
                ]));
            }
        }
    }
}
