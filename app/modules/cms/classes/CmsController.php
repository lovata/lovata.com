<?php namespace Cms\Classes;

use App;
use Cms;
use Site;
use Config;
use Request;
use Redirect;
use Illuminate\Routing\Controller as ControllerBase;
use Closure;

/**
 * CmsController is the master controller for all front-end pages.
 * All requests that have not been picked up already by the router will end up here,
 * then the URL is passed to the front-end controller for processing.
 *
 * @see Cms\Classes\Controller Front-end controller class
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class CmsController extends ControllerBase
{
    use \October\Rain\Extension\ExtendableTrait;

    /**
     * @var array implement behaviors in this controller.
     */
    public $implement;

    /**
     * __construct a new CmsController instance.
     */
    public function __construct()
    {
        $this->extendableConstruct();
    }

    /**
     * extend this object properties upon construction.
     * @param Closure $callback
     */
    public static function extend(Closure $callback)
    {
        self::extendableExtendCallback($callback);
    }

    /**
     * run finds and serves the request using the primary controller.
     * @param string $url Specifies the requested page URL.
     * If the parameter is omitted, the current URL used.
     * @return string Returns the processed page content.
     */
    public function run($url = '/')
    {
        // Check configuration for bypass exceptions
        if (Cms::urlHasException((string) $url, 'site')) {
            return App::make(Controller::class)->run($url);
        }

        // Locate site
        $site = $this->findSite(Request::getHost(), $url);

        // Remove prefix, if applicable
        $uri = $this->parseUri($site, $url);

        // Enforce prefix, if applicable
        if ($redirect = $this->redirectWithoutPrefix($site, $url, $uri)) {
            return $redirect;
        }

        return App::make(Controller::class)->run($uri);
    }

    /**
     * findSite locates the site based on the current URL
     */
    protected function findSite(string $host, string $url)
    {
        $site = Site::getSiteFromRequest($host, $url);

        if (!$site || !$site->is_enabled) {
            abort(404);
        }

        Site::setActiveSite($site);
        Site::applyActiveSite($site);

        return $site;
    }

    /**
     * parseUri removes the prefix from a URL
     */
    protected function parseUri($site, string $url): string
    {
        return $site ? $site->removeRoutePrefix($url) : $url;
    }

    /**
     * redirectWithoutPrefix redirects if a prefix is enforced
     */
    protected function redirectWithoutPrefix($site, string $originalUrl, string $proposedUrl)
    {
        // Only the primary site should redirect
        if (!$site || !$site->is_primary || !$site->is_prefixed) {
            return null;
        }

        // A prefix has been found and removed already
        if ($originalUrl !== '/' && $originalUrl !== $proposedUrl) {
            return null;
        }

        // Apply redirect policy
        $site = $this->determineSiteFromPolicy($site);

        // Preserve query string
        $redirectUrl = $site->attachRoutePrefix($originalUrl);
        if ($queryString = Request::getQueryString()) {
            $redirectUrl .= '?'.$queryString;
        }

        // No prefix detected, attach one with redirect
        return Redirect::to($redirectUrl, 301);
    }

    /**
     * determineSiteFromPolicy returns a site based on the configuration
     */
    protected function determineSiteFromPolicy($primary)
    {
        $policy = Config::get('cms.redirect_policy', 'detect');

        // Use primary site
        if ($policy === 'primary') {
            return $primary;
        }

        // Detect site from browser locale
        if ($policy === 'detect') {
            return Site::getSiteFromBrowser((string) Request::server('HTTP_ACCEPT_LANGUAGE'));
        }

        // Use a specified site ID
        return Site::getSiteFromId($policy) ?: $primary;
    }
}
