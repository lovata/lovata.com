<?php namespace Cms\Helpers;

use Url;
use Site;
use View;
use Route;
use Config;
use Redirect;
use Response;
use Cms\Classes\Controller;
use System\Helpers\DateTime as DateTimeHelper;
use Exception;

/**
 * Cms Helper
 *
 * @package october\cms
 * @see \Cms\Facades\Cms
 * @author Alexey Bobkov, Samuel Georges
 */
class Cms
{
    use \Cms\Helpers\Cms\HasSites;

    /**
     * @var bool actionExists determines if the run action exists
     */
    protected static $actionExists;

    /**
     * @var string urlPathPrefix prefixes every URL
     */
    protected $urlPathPrefix;

    /**
     * setUrlPrefix sets a prefix for every URL
     */
    public function setUrlPrefix(string $prefix)
    {
        $this->urlPathPrefix = trim($prefix, '/');
    }

    /**
     * url returns a URL in context of the frontend
     */
    public function url($path = null)
    {
        // Process path
        if (substr($path, 0, 1) === '/') {
            $path = substr($path, 1);
        }

        if ($this->urlPathPrefix !== null) {
            $path = $this->urlPathPrefix . '/' . $path;
        }

        // Use the router
        $routeAction = 'Cms\Classes\CmsController@run';

        if (self::$actionExists === null) {
            self::$actionExists = Route::getRoutes()->getByAction($routeAction) !== null;
        }

        if (self::$actionExists) {
            return Url::action($routeAction, ['slug' => $path]);
        }

        // Use the base URL
        return Url::to($path);
    }

    /**
     * pageUrl returns a URL for a CMS page, this fires up the CMS controller
     * to generate the value for maximum performance.
     */
    public function pageUrl($name, $parameters = [])
    {
        $controller = (Controller::getController() ?: new Controller);

        return $controller->pageUrl($name, $parameters, false);
    }

    /**
     * fullUrl returns a complete URL considering the current site context
     * and base URL, including prefix and hostname.
     */
    public function fullUrl($path = null)
    {
        $path = '/' . ltrim($path, '/');

        if ($site = Site::getSiteFromContext()) {
            return rtrim($site->base_url . $path, '/');
        }

        return $this->url($path);
    }

    /**
     * redirect creates a new redirect to a CMS page or URL, the second argument
     * contains the CMS page parameters or the status code.
     */
    public function redirect($to, $parameters = [], $status = 302)
    {
        if (is_int($parameters)) {
            $status = $parameters;
            $parameters = [];
        }

        $url = $this->pageUrl($to, $parameters) ?: $to;

        return Redirect::to($url, $status);
    }

    /**
     * pageNotFound returns a 404 page response
     */
    public function pageNotFound()
    {
        try {
            $controller = (Controller::getController() ?: new Controller);

            $router = $controller->getRouter();

            if ($router->findByUrl('/404')) {
                return $controller->run('/404');
            }
        }
        catch (Exception $ex) {
        }

        return Response::make(View::make('cms::404'), 404);
    }

    /**
     * pageError returns a 500 page response
     */
    public function pageError()
    {
        try {
            $controller = (Controller::getController() ?: new Controller);

            $router = $controller->getRouter();

            if ($router->findByUrl('/error')) {
                return $controller->run('/error');
            }
        }
        catch (Exception $ex) {
        }

        return Response::make(View::make('cms::error'), 500);
    }

    /**
     * makeCarbon converts mixed inputs to a Carbon object and sets the CMS timezone
     * @return \Carbon\Carbon
     */
    public function makeCarbon($value, $throwException = true)
    {
        $carbon = DateTimeHelper::makeCarbon($value, $throwException);

        $carbon->setTimezone(Config::get('cms.timezone', Config::get('app.timezone')));

        return $carbon;
    }
}
