<?php namespace Backend\Behaviors;

use Lang;
use Backend;
use BackendAuth;
use Backend\Classes\ControllerBehavior;
use ApplicationException;
use ForbiddenException;

/**
 * ImportExportController adds features for importing and exporting data.
 *
 * This behavior is implemented in the controller like so:
 *
 *     public $implement = [
 *         \Backend\Behaviors\ImportExportController::class,
 *     ];
 *
 *     public $importExportConfig = 'config_import_export.yaml';
 *
 * The `$importExportConfig` property makes reference to the configuration
 * values as either a YAML file, located in the controller view directory,
 * or directly as a PHP array.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ImportExportController extends ControllerBehavior
{
    use \Backend\Behaviors\ImportExportController\CanFormatCsv;
    use \Backend\Behaviors\ImportExportController\CanFormatJson;
    use \Backend\Behaviors\ImportExportController\HasImportMode;
    use \Backend\Behaviors\ImportExportController\HasExportMode;
    use \Backend\Behaviors\ImportExportController\HasListExport;

    /**
     * @inheritDoc
     */
    protected $requiredProperties = ['importExportConfig'];

    /**
     * @var array requiredConfig values that must exist when applying the primary config file.
     */
    protected $requiredConfig = [];

    /**
     * @var array actions visible in context of the controller
     */
    protected $actions = ['import', 'export', 'download'];

    /**
     * __construct the behavior
     * @param Backend\Classes\Controller $controller
     */
    public function __construct($controller)
    {
        parent::__construct($controller);

        // Build configuration
        $this->setConfig($controller->importExportConfig, $this->requiredConfig);
    }

    /**
     * beforeDisplay fires before the page is displayed and AJAX is executed.
     */
    public function beforeDisplay()
    {
        // Import form widgets
        if ($this->importUploadFormWidget = $this->makeImportUploadFormWidget()) {
            $this->importUploadFormWidget->bindToController();
        }

        if ($this->importOptionsFormWidget = $this->makeImportOptionsFormWidget()) {
            $this->importOptionsFormWidget->bindToController();
        }

        // Export form widgets
        if ($this->exportFormatFormWidget = $this->makeExportFormatFormWidget()) {
            $this->exportFormatFormWidget->bindToController();
        }

        if ($this->exportOptionsFormWidget = $this->makeExportOptionsFormWidget()) {
            $this->exportOptionsFormWidget->bindToController();
        }
    }

    /**
     * importExportMakePartial controller accessor for making partials within this behavior.
     * @param string $partial
     * @param array $params
     * @return string Partial contents
     */
    public function importExportMakePartial($partial, $params = [])
    {
        $contents = $this->controller->makePartial('import_export_'.$partial, $params + $this->vars, false);

        if (!$contents) {
            $contents = $this->makePartial($partial, $params);
        }

        return $contents;
    }

    /**
     * checkPermissionsForType checks to see if the import/export is controlled by permissions
     * and if the logged in user has permissions.
     */
    protected function checkPermissionsForType($type)
    {
        if (
            ($permissions = $this->getConfig($type.'[permissions]')) &&
            (!BackendAuth::getUser()->hasAnyAccess((array) $permissions))
        ) {
            throw new ForbiddenException;
        }
    }

    /**
     * makeOptionsFormWidgetForType
     */
    protected function makeOptionsFormWidgetForType($type)
    {
        if (!$this->getConfig($type)) {
            return null;
        }

        $fieldConfig = $this->getConfig($type.'[form]');
        if ($fieldConfig !== null) {
            $widgetConfig = $this->makeConfig($fieldConfig);
            $widgetConfig->model = $this->getModelForType($type);
            $widgetConfig->alias = $type.'OptionsForm';
            $widgetConfig->arrayName = ucfirst($type).'Options';

            return $this->makeWidget(\Backend\Widgets\Form::class, $widgetConfig);
        }

        return null;
    }

    /**
     * getModelForType
     */
    protected function getModelForType($type)
    {
        $cacheProperty = $type.'Model';

        if ($this->{$cacheProperty} !== null) {
            return $this->{$cacheProperty};
        }

        $modelClass = $this->getConfig($type.'[modelClass]');
        if (!$modelClass) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.missing_model_class_error', [
                'type' => $type
            ]));
        }

        $model = new $modelClass;
        $this->controller->importExportExtendModel($model);

        return $this->{$cacheProperty} = $model;
    }

    /**
     * makeListColumns
     */
    protected function makeListColumns($config, $model)
    {
        $config = $this->makeConfig($config);
        $config->model = $model;

        $widget = $this->makeWidget(\Backend\Widgets\Lists::class, $config);
        $columns = $widget->getColumns();

        if (!isset($columns) || !is_array($columns)) {
            return null;
        }

        $result = [];
        foreach ($columns as $attribute => $column) {
            $result[$attribute] = $column->label;
        }

        return $result;
    }

    /**
     * getRedirectUrlForType
     */
    protected function getRedirectUrlForType($type = null)
    {
        $redirect = $this->getConfig($type.'[redirect]');

        if ($redirect !== null) {
            return $redirect ? Backend::url($redirect) : 'javascript:;';
        }

        return $this->controller->actionUrl($type);
    }

    /**
     * getFormatOptionsForPost returns the file format options from postback. This method
     * can be used to define presets.
     */
    protected function getFormatOptionsForPost(): array
    {
        $defaults = [
            'file_format' => 'json',
            'format_delimiter' => ',',
            'format_enclosure' => '"',
            'format_escape' => '\\',
            'format_encoding' => 'UTF-8',
            'first_row_titles' => true,
        ];

        return [
            'file_format' => post('file_format', $this->getConfig('defaultFormatOptions[fileFormat]', $defaults['file_format'])),
            'format_delimiter' => post('format_delimiter', $this->getConfig('defaultFormatOptions[delimiter]', $defaults['format_delimiter'])),
            'format_enclosure' => post('format_enclosure', $this->getConfig('defaultFormatOptions[enclosure]', $defaults['format_enclosure'])),
            'format_escape' => post('format_escape', $this->getConfig('defaultFormatOptions[escape]', $defaults['format_escape'])),
            'format_encoding' => post('format_encoding', $this->getConfig('defaultFormatOptions[encoding]', $defaults['format_encoding'])),
            'first_row_titles' => post('first_row_titles', $this->getConfig('defaultFormatOptions[firstRowTitles]', $defaults['first_row_titles'])),
        ];
    }

    /**
     * getFormatOptionsForModel returns the file format options used by models.
     */
    protected function getFormatOptionsForModel(): array
    {
        $options = [
            'fileFormat' => post('file_format', $this->getConfig('defaultFormatOptions[fileFormat]')),
            'delimiter' => post('format_delimiter', $this->getConfig('defaultFormatOptions[delimiter]')),
            'enclosure' => post('format_enclosure', $this->getConfig('defaultFormatOptions[enclosure]')),
            'escape' => post('format_escape', $this->getConfig('defaultFormatOptions[escape]')),
            'encoding' => post('format_encoding', $this->getConfig('defaultFormatOptions[encoding]')),
            'firstRowTitles' => (bool) post('first_row_titles', $this->getConfig('defaultFormatOptions[firstRowTitles]', true)),
            'customJson' => $this->getConfig('defaultFormatOptions[customJson]'),
        ];

        if ($options['fileFormat'] !== 'csv_custom') {
            $options['delimiter'] = null;
            $options['enclosure'] = null;
            $options['escape'] = null;
            $options['encoding'] = null;
        }

        return $options;
    }

    /**
     * isCustomFileFormat returns true if the process is using a custom format
     * via `customJson` or otherwise.
     */
    protected function isCustomFileFormat()
    {
        if (!$fileFormat = post('file_format', 'json')) {
            return false;
        }

        if ($fileFormat !== 'json') {
            return false;
        }

        return (bool) $this->getFormatOptionsForModel()['customJson'];
    }

    //
    // Overrides
    //

    /**
     * importExportGetFileName
     * @return string
     */
    public function importExportGetFileName()
    {
        return $this->exportFileName;
    }

    /**
     * importExportExtendModel
     * @param Model $model
     * @return Model
     */
    public function importExportExtendModel($model)
    {
        return $model;
    }

    /**
     * importExportExtendColumns
     */
    public function importExportExtendColumns($columns, $context = null)
    {
        return $columns;
    }
}
