<?php namespace Backend\Facades;

use October\Rain\Support\Facade;

/**
 * Backend facade
 *
 * @method static string assetVersion()
 * @method static string uri()
 * @method static string url(string $path = null, mixed $parameters = [], bool|null $secure = null)
 * @method static string baseUrl(string $path = null)
 * @method static string skinAsset(string $path = null)
 * @method static \Illuminate\Http\RedirectResponse redirect(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \Illuminate\Http\RedirectResponse redirectGuest(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \Illuminate\Http\RedirectResponse redirectIntended(string $path, int $status = 302, array $headers = [], bool|null $secure = null)
 * @method static \Carbon\Carbon makeCarbon(mixed $value, bool $throwException = true)
 * @method static string date(mixed $dateTime, array $options = [])
 * @method static string dateTime(mixed $dateTime, array $options = [])
 *
 * @see \Backend\Helpers\Backend
 */
class Backend extends Facade
{
    /**
     * getFacadeAccessor returns the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.helper';
    }
}
