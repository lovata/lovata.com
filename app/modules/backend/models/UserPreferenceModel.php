<?php namespace Backend\Models;

use Site;
use System\Models\SettingModel;

/**
 * UserPreferenceModel base class, same as SettingModel except values are
 * saved with the logged in user's preferences via Backend\Models\UserPreference
 *
 * Add this the model class definition:
 *
 *     public $settingsCode = 'author_plugin_code';
 *     public $settingsFields = 'fields.yaml';
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class UserPreferenceModel extends SettingModel
{
    /**
     * @var string table associated with the model
     */
    protected $table = 'backend_user_preferences';

    /**
     * @var array expandoPassthru attributes that should not be serialized
     */
    protected $expandoPassthru = ['user_id', 'namespace', 'group', 'item', 'site_id', 'site_root_id'];

    /**
     * settingBeforeSave
     */
    public function settingBeforeSave()
    {
        $preferences = UserPreference::forUser();

        [$namespace, $group, $item] = $preferences->parseKey($this->settingsCode);

        $this->item = $item;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->user_id = $preferences->userContext->id;
    }

    /**
     * getSettingsRecord returns the raw Model record that stores the settings.
     * @return Model
     */
    public function getSettingsRecord()
    {
        $record = $this->newQuery()
            ->remember(1440, $this->getCacheKey())
            ->first();

        return $record ?: null;
    }

    /**
     * newQuery applies a local scope
     */
    public function newQuery()
    {
        $query = $this->registerGlobalScopes($this->newQueryWithoutScopes());

        $item = UserPreference::forUser();

        return $item->scopeApplyKeyAndUser($query, $this->settingsCode, $item->userContext);
    }

    /**
     * getCacheKey returns a cache key for this record.
     */
    public function getCacheKey()
    {
        $item = UserPreference::forUser();

        $userId = $item->userContext ? $item->userContext->id : 0;

        $key = 'backend::userpreference.' . $this->settingsCode . '-' . $userId . '-' . Site::getSiteIdFromContext();

        if ($this->isClassInstanceOf(\October\Contracts\Database\MultisiteInterface::class)) {
            $key .= '-' . ($this->site_id ?: Site::getSiteIdFromContext());
        }

        return $key;
    }
}
