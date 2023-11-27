<?php namespace Cms\Facades;

use October\Rain\Support\Facade;

/**
 * Cms facade
 *
 * @method static string url(string $path = null)
 * @method static string pageUrl(string $name, array $parameters = [])
 * @method static string fullUrl(string $path = null)
 * @method static string pageNotFound()
 * @method static string pageError()
 * @method static \Illuminate\Http\RedirectResponse redirect(string $to, array $parameters = [], int $status = 302)
 * @method static \Carbon\Carbon makeCarbon(mixed $value, bool $throwException = true)
 *
 * @see \Cms\Helpers\Cms
 */
class Cms extends Facade
{
    /**
     * getFacadeAccessor returns the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cms.helper';
    }
}
