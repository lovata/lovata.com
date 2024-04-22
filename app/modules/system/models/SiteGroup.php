<?php namespace System\Models;

use Site;
use Model;
use ValidationException;

/**
 * SiteGroup
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class SiteGroup extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'system_site_groups';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'code' => 'required',
        'name' => 'required',
    ];

    /**
     * @var array hasMany
     */
    public $hasMany = [
        'sites' => [SiteDefinition::class, 'key' => 'group_id'],
    ];

    /**
     * isConfigured returns true if a group has been configured
     */
    public static function isConfigured()
    {
        return static::count() > 0;
    }

    /**
     * beforeDelete
     */
    public function beforeDelete()
    {
        if (($count = $this->sites()->count()) > 0) {
            throw new ValidationException(['name' => __("Unable to delete site group because it is being used by existing site definitions (:count).", ['count' => $count])]);
        }
    }

    /**
     * afterSave
     */
    public function afterSave()
    {
        Site::resetCache();
    }

    /**
     * afterCreate assigns orphaned sites to this group
     */
    public function afterCreate()
    {
        SiteDefinition::where('group_id', null)->update(['group_id' => $this->id]);
    }
}
