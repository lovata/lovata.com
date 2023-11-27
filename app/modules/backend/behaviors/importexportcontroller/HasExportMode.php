<?php namespace Backend\Behaviors\ImportExportController;

use Lang;
use File;
use Backend;
use Illuminate\Database\Eloquent\MassAssignmentException;
use ApplicationException;
use Exception;

/**
 * HasExportMode contains logic for exports
 */
trait HasExportMode
{
    /**
     * @var Model exportModel
     */
    public $exportModel;

    /**
     * @var array exportColumns configuration.
     */
    public $exportColumns;

    /**
     * @var string exportFileName used for export output.
     */
    protected $exportFileName;

    /**
     * @var Backend\Classes\WidgetBase exportFormatFormWidget reference to the widget used for standard export options.
     */
    protected $exportFormatFormWidget;

    /**
     * @var Backend\Classes\WidgetBase exportOptionsFormWidget reference to the widget used for custom export options.
     */
    protected $exportOptionsFormWidget;

    /**
     * export action
     */
    public function export()
    {
        if ($response = $this->checkPermissionsForType('export')) {
            return $response;
        }

        if ($response = $this->checkUseListExportMode()) {
            return $response;
        }

        $this->addJs('js/october.export.js');
        $this->addCss('css/export.css');

        $this->controller->pageTitle = $this->controller->pageTitle
            ?: Lang::get($this->getConfig('export[title]', 'Export Records'));

        $this->prepareExportVars();
    }

    /**
     * download action
     */
    public function download($name, $outputName = null)
    {
        $this->controller->pageTitle = $this->controller->pageTitle
            ?: Lang::get($this->getConfig('export[title]', 'Export Records'));

        return $this->exportGetModel()->download($name, $outputName);
    }

    /**
     * onExport
     */
    public function onExport()
    {
        try {
            $model = $this->exportGetModel();
            $columns = $this->processExportColumnsFromPost();

            if ($optionData = post('ExportOptions')) {
                $model->fill($optionData);
            }

            $exportOptions = $this->getFormatOptionsForModel();
            $exportOptions['sessionKey'] = $this->exportFormatFormWidget->getSessionKey();

            $model->file_format = $exportOptions['fileFormat'] ?? 'json';
            $reference = $model->export($columns, $exportOptions);

            $fileUrl = $this->controller->actionUrl(
                'download',
                $reference.'/'.$this->makeExportFileName($model->file_format)
            );

            $this->vars['fileUrl'] = $fileUrl;
            $this->vars['returnUrl'] = $this->getRedirectUrlForType('export');
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

        return $this->importExportMakePartial('export_result_form');
    }

    /**
     * makeExportFileName
     */
    protected function makeExportFileName($mode = 'json')
    {
        // Locate filename
        $fileName = $this->controller->importExportGetFileName();
        if (!$fileName) {
            $fileName = $this->getConfig('export[fileName]', 'export');
        }

        // Remove extension
        $fileName = File::name($fileName);

        $extension = 'csv';
        if ($mode === 'json') {
            $extension = 'json';
        }

        return $fileName . '.' . $extension;
    }

    /**
     * onExportLoadForm
     */
    public function onExportLoadForm()
    {
        return $this->importExportMakePartial('export_form');
    }

    /**
     * prepareExportVars for the view data.
     * @return void
     */
    public function prepareExportVars()
    {
        $this->vars['exportFormatFormWidget'] = $this->exportFormatFormWidget;
        $this->vars['exportOptionsFormWidget'] = $this->exportOptionsFormWidget;
        $this->vars['exportColumns'] = $this->getExportColumns();
        $this->vars['exportCustomFormat'] = $this->isCustomFileFormat();

        // Make these variables available to widgets
        $this->controller->vars += $this->vars;
    }

    /**
     * exportRender
     */
    public function exportRender()
    {
        return $this->importExportMakePartial('container_export');
    }

    /**
     * exportGetModel
     */
    public function exportGetModel()
    {
        return $this->getModelForType('export');
    }

    /**
     * getExportColumns
     */
    protected function getExportColumns()
    {
        if ($this->exportColumns !== null) {
            return $this->exportColumns;
        }

        $columnConfig = $this->getConfig('export[list]');

        $columns = $this->makeListColumns($columnConfig, $this->exportGetModel());

        $columns = $this->controller->importExportExtendColumns($columns, 'export');

        if (empty($columns)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.empty_export_columns_error'));
        }

        return $this->exportColumns = $columns;
    }

    /**
     * makeExportFormatFormWidget
     */
    protected function makeExportFormatFormWidget()
    {
        if (!$this->getConfig('export') || $this->getConfig('export[useList]')) {
            return null;
        }

        $widgetConfig = $this->makeConfig('~/modules/backend/behaviors/importexportcontroller/partials/fields_export.yaml');
        $widgetConfig->model = $this->exportGetModel();
        $widgetConfig->alias = 'exportUploadForm';
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
     * makeExportOptionsFormWidget
     */
    protected function makeExportOptionsFormWidget()
    {
        $widget = $this->makeOptionsFormWidgetForType('export');

        if (!$widget && $this->exportFormatFormWidget) {
            $stepSection = $this->exportFormatFormWidget->getField('step3_section');
            $stepSection->hidden = true;
        }

        return $widget;
    }

    /**
     * processExportColumnsFromPost
     */
    protected function processExportColumnsFromPost()
    {
        $visibleColumns = post('visible_columns', []);
        $columns = post('export_columns', []);

        foreach ($columns as $key => $columnName) {
            if (!isset($visibleColumns[$columnName])) {
                unset($columns[$key]);
            }
        }

        $result = [];
        $definitions = $this->getExportColumns();

        foreach ($columns as $column) {
            if (isset($definitions[$column])) {
                $result[$column] = $definitions[$column];
            }
        }

        return $result;
    }
}
