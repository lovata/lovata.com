<?php namespace System\Models;

use Lang;
use Model;
use System\Classes\PluginManager;

/**
 * PluginVersion stores information about current plugin versions.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginVersion extends Model
{
    public $table = 'system_plugin_versions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var bool timestamps enabled
     */
    public $timestamps = false;

    /**
     * @var bool Plugin has been disabled by a missing dependency.
     */
    public $disabledBySystem = false;

    /**
     * @var bool Plugin has been disabled by the user or configuration.
     */
    public $disabledByConfig = false;

    /**
     * @var bool If true, plugin exists in the database but not the filesystem.
     */
    public $orphaned = false;

    /**
     * @var string Plugin name, sourced from plugin details
     */
    public $name;

    /**
     * @var string Plugin description, sourced from plugin details
     */
    public $description;

    /**
     * @var string Plugin author, sourced from plugin details
     */
    public $author;

    /**
     * @var string Plugin icon, sourced from plugin details
     */
    public $icon;

    /**
     * @var string Plugin homepage, sourced from plugin details
     */
    public $homepage;

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ['slug'];

    /**
     * afterFetch
     */
    public function afterFetch()
    {
        // Override the database columns with the plugin details
        // found in the plugin registration file.
        $manager = PluginManager::instance();
        $pluginObj = $manager->findByIdentifier($this->code);

        if (!$pluginObj) {
            $this->name = $this->code;
            $this->description = Lang::get('system::lang.plugins.unknown_plugin');
            $this->orphaned = true;
            return;
        }

        if ($pluginObj->disabled) {
            $this->name = $this->code;
            $this->disabledBySystem = true;
            $this->disabledByConfig = in_array($this->code, $manager->listDisabledByConfig());
            $this->description = $this->disabledByConfig || $this->is_disabled
                ? Lang::get('system::lang.plugins.disabled_by_config')
                : Lang::get('system::lang.plugins.disabled_by_system');
            return;
        }

        $pluginInfo = $pluginObj->pluginDetails();
        foreach ($pluginInfo as $attribute => $info) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = Lang::get($info);
            }
        }
    }

    /**
     * getIsUpdatableAttribute returns true if the plugin should be updated by the system.
     * @return bool
     */
    public function getIsUpdatableAttribute()
    {
        return !$this->is_disabled && !$this->disabledBySystem && !$this->disabledByConfig;
    }

    /**
     * scopeApplyEnabled to only include enabled plugins
     * @param $query
     * @return mixed
     */
    public function scopeApplyEnabled($query)
    {
        return $query->where('is_disabled', '!=', 1);
    }

    /**
     * getSlugAttribute provides the slug attribute.
     */
    public function getSlugAttribute()
    {
        return self::makeSlug($this->code);
    }

    /**
     * makeSlug generates a slug for the plugin.
     */
    public static function makeSlug($code)
    {
        return strtolower(str_replace('.', '-', $code));
    }
}
