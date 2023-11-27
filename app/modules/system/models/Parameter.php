<?php namespace System\Models;

use Cache;
use System;
use October\Rain\Database\Model;

/**
 * Parameter model is used for storing internal application parameters.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Parameter extends Model
{
    use \October\Rain\Support\Traits\KeyParser;

    /**
     * @var string table associated with the model
     */
    protected $table = 'system_parameters';

    /**
     * @var bool timestamps enabled
     */
    public $timestamps = false;

    /**
     * @var array cache is an internal cache of model values.
     */
    protected static $cache = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = ['value'];

    /**
     * afterSave clears the cache after saving.
     */
    public function afterSave()
    {
        $this->clearCache();
    }

    /**
     * get returns a setting value by the module (or plugin) name and setting name.
     * @param string $key Specifies the setting key value, for example 'system:updates.check'
     * @param mixed $default The default value to return if the setting doesn't exist in the DB.
     * @return mixed Returns the setting value loaded from the database or the default value.
     */
    public static function get($key, $default = null)
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        $record = static::findRecord($key);
        if (!$record) {
            return static::$cache[$key] = $default;
        }

        return static::$cache[$key] = $record->value;
    }

    /**
     * set stores a setting value to the database.
     * @param string $key Specifies the setting key value, for example 'system:updates.check'
     * @param mixed $value The setting value to store, serializable.
     * @return bool
     */
    public static function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_value) {
                static::set($_key, $_value);
            }
            return true;
        }

        $record = static::findRecord($key);
        if (!$record) {
            $record = new static;
            [$namespace, $group, $item] = $record->parseKey($key);
            $record->namespace = $namespace;
            $record->group = $group;
            $record->item = $item;
        }

        $record->value = $value;
        $record->save();

        static::$cache[$key] = $value;
        return true;
    }

    /**
     * reset a setting value by deleting the record.
     * @param string $key Specifies the setting key value.
     * @return bool
     */
    public function reset($key)
    {
        $record = static::findRecord($key);
        if (!$record) {
            return false;
        }

        $record->delete();

        unset(static::$cache[$key]);
        return true;
    }

    /**
     * clearCache
     */
    public function clearCache()
    {
        Cache::forget($this->getCacheKey());
    }

    /**
     * findRecord returns a record with cache
     * @return self
     */
    public static function findRecord($key)
    {
        if (!System::hasDatabase()) {
            return null;
        }

        $record = new static;

        return $record
            ->applyKey($key)
            ->remember(5, $record->getCacheKey($key))
            ->first()
        ;
    }

    /**
     * scopeApplyKey is a scope to find a setting record for the specified module
     * (or plugin) name and setting name. Key specifies the setting key value,
     * for example 'system:updates.check'. The default value to return if the setting
     * doesn't exist in the DB.
     * @param string $key
     * @param mixed $default
     * @return QueryBuilder
     */
    public function scopeApplyKey($query, $key)
    {
        [$namespace, $group, $item] = $this->parseKey($key);

        $query = $query
            ->where('namespace', $namespace)
            ->where('group', $group)
            ->where('item', $item)
        ;

        return $query;
    }

    /**
     * getCacheKey returns a cache key for this record.
     */
    public function getCacheKey($key = null)
    {
        if ($key !== null) {
            [$namespace, $group, $item] = $this->parseKey($key);

            return implode('-', [$this->table, $namespace, $group, $item]);
        }

        return implode('-', [$this->table, $this->namespace, $this->group, $this->item]);
    }

    /**
     * clearInternalCache of model cache values.
     * @return void
     */
    public static function clearInternalCache()
    {
        static::$cache = [];
    }
}
