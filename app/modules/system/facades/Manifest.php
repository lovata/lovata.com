<?php namespace System\Facades;

use October\Rain\Support\Facade;

/**
 * Manifest facade
 *
 * @method static bool has(string $name)
 * @method static void get(string $key, mixed $default)
 * @method static void put(string $key, mixed $value)
 * @method static void forget(string $name)
 * @method static void build()
 *
 * @see \System\Classes\ManifestCache
 */
class Manifest extends Facade
{
    /**
     * getFacadeAccessor gets the registered name of the component.
     */
    protected static function getFacadeAccessor()
    {
        return 'system.manifest';
    }
}
