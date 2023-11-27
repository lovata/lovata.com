<?php namespace System\Behaviors;

use Log;
use Cache;
use System;
use Artisan;
use Exception;
use System\Classes\ModelBehavior;

/**
 * SettingsModel extension
 *
 * Add this the model class definition:
 *
 *     public $implement = [\System\Behaviors\SettingsModel::class];
 *     public $settingsCode = 'author_plugin_code';
 *     public $settingsFields = 'fields.yaml';
 *
 * @todo This class will be deprecated soon
 * @see System\Models\SettingModel
 */
class SettingsModel extends ModelBehavior
{
    use \System\Traits\ConfigMaker;

    /**
     * @var string recordCode
     */
    protected $recordCode;

    /**
     * @var array fieldConfig
     */
    protected $fieldConfig;

    /**
     * @var array fieldValues
     */
    protected $fieldValues = [];

    /**
     * @var array Internal cache of model objects.
     */
    private static $instances = [];

    /**
     * @inheritDoc
     */
    protected $requiredProperties = ['settingsFields', 'settingsCode'];

    /**
     * __construct the settings instance
     */
    public function __construct($model)
    {
        parent::__construct($model);

        $this->model->setTable('system_settings');
        $this->model->jsonable(['value']);
        $this->model->guard([]);
        $this->model->timestamps = false;

        $this->configPath = $this->guessConfigPathFrom($model);

        // Access to model's overrides is unavailable, using events instead
        $this->model->bindEvent('model.afterFetch', [$this, 'afterModelFetch']);
        $this->model->bindEvent('model.beforeSave', [$this, 'beforeModelSave']);
        $this->model->bindEvent('model.afterSave', [$this, 'afterModelSave']);
        $this->model->bindEvent('model.setAttribute', [$this, 'setSettingsValue']);

        // Process attributes last for traits with attribute modifiers
        $this->model->bindEvent('model.saveInternal', [$this, 'saveModelInternal'], -1);

        // Parse the config
        $this->recordCode = $this->model->settingsCode;
    }

    /**
     * instance of the settings model, intended as a static method
     */
    public function instance()
    {
        if (isset(self::$instances[$this->recordCode])) {
            return self::$instances[$this->recordCode];
        }

        $item = $this->getSettingsRecord();
        if (!$item) {
            $this->model->initSettingsData();
            $item = $this->model;
        }

        return self::$instances[$this->recordCode] = $item;
    }

    /**
     * resetDefault, this will delete the record model
     */
    public function resetDefault()
    {
        $record = $this->getSettingsRecord();
        if (!$record) {
            return;
        }

        $record->delete();
        unset(self::$instances[$this->recordCode]);
        Cache::forget($this->getCacheKey());
    }

    /**
     * isConfigured checks if the model has been set up previously, intended as a static method
     * @return bool
     */
    public function isConfigured()
    {
        return $this->getSettingsRecord() !== null;
    }

    /**
     * getSettingsRecord returns the raw Model record that stores the settings.
     * @return Model
     */
    public function getSettingsRecord()
    {
        if (!System::hasDatabase()) {
            return null;
        }

        $record = $this->model
            ->where('item', $this->recordCode)
            ->remember(1440, $this->getCacheKey())
            ->first();

        return $record ?: null;
    }

    /**
     * set a single or array key pair of values, intended as a static method
     */
    public function set($key, $value = null)
    {
        $data = is_array($key) ? $key : [$key => $value];
        $obj = $this->instance();
        $obj->fill($data);
        return $obj->save();
    }

    /**
     * get helper for getSettingsValue, intended as a static method
     */
    public function get($key, $default = null)
    {
        return $this->instance()->getSettingsValue($key, $default);
    }

    /**
     * getSettingsValue gets a single setting value, or return a default value
     */
    public function getSettingsValue($key, $default = null)
    {
        if (array_key_exists($key, $this->fieldValues)) {
            return $this->fieldValues[$key];
        }

        return array_get($this->fieldValues, $key, $default);
    }

    /**
     * setSettingsValue sets a single setting value, if allowed.
     */
    public function setSettingsValue($key, $value)
    {
        if ($this->isKeyAllowed($key)) {
            return;
        }

        $this->model->attributes[$key] = $this->fieldValues[$key] = $value;
    }

    /**
     * initSettingsData default values to set for this model, override
     */
    public function initSettingsData()
    {
    }

    /**
     * afterModelFetch populates the field values from the database record.
     */
    public function afterModelFetch()
    {
        $this->fieldValues = $this->model->value ?: [];
        $this->model->attributes = array_merge($this->fieldValues, $this->model->attributes);
        $this->model->syncOriginal();
    }

    /**
     * saveModelInternal method for the model
     * @return void
     */
    public function saveModelInternal()
    {
        // Reset values from the attributes that may be manipulated elsewhere
        if ($this->fieldValues) {
            foreach ($this->fieldValues as $key => $val) {
                if (array_key_exists($key, $this->model->attributes)) {
                    $this->fieldValues[$key] = $this->model->attributes[$key];
                }
            }
        }

        // Purge the field values from the attributes
        $this->model->attributes = array_diff_key($this->model->attributes, $this->fieldValues);
    }

    /**
     * beforeModelSave, ensure the record code is set and the jsonable field values
     */
    public function beforeModelSave()
    {
        $this->model->item = $this->recordCode;
        if ($this->fieldValues) {
            $this->model->value = $this->fieldValues;
        }
    }

    /**
     * afterModelSave, clear the cached query entry
     * and restart queue workers so they have the latest settings
     * @return void
     */
    public function afterModelSave()
    {
        Cache::forget($this->getCacheKey());

        try {
            Artisan::call('queue:restart');
        }
        catch (Exception $e) {
            Log::warning($e->getMessage());
        }
    }

    /**
     * isKeyAllowed checks if a key is legitimate or should be added to
     * the field value collection
     */
    protected function isKeyAllowed($key)
    {
        // Let the core columns through
        if ($key == 'id' || $key == 'value' || $key == 'item') {
            return true;
        }

        // Let relations through
        if ($this->model->hasRelation($key)) {
            return true;
        }

        return false;
    }

    /**
     * getFieldConfig returns the field configuration used by this model.
     */
    public function getFieldConfig()
    {
        if ($this->fieldConfig !== null) {
            return $this->fieldConfig;
        }

        return $this->fieldConfig = $this->makeConfig($this->model->settingsFields);
    }

    /**
     * getCacheKey returns a cache key for this record.
     */
    protected function getCacheKey()
    {
        return 'system::settings.'.$this->recordCode;
    }

    /**
     * clearInternalCache of model instances.
     * @return void
     */
    public static function clearInternalCache()
    {
        static::$instances = [];
    }
}
