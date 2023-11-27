<?php namespace System\Middleware;

use Closure;
use Site;
use Url;

/**
 * ActiveSite sets the active site based on the request parameters
 */
class ActiveSite
{
    /**
     * handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $site = Site::getSiteFromRequest($request->getHost(), $this->getRoutedUri($request));

        if ($site && $site->is_enabled) {
            Site::setActiveSite($site);
            Site::applyActiveSite($site);
        }

        return $next($request);
    }

    /**
     * getRoutedUri
     */
    protected function getRoutedUri($request)
    {
        $rootUri = trim(parse_url(Url::to(''), PHP_URL_PATH), '/');
        $fullUri = trim(parse_url($request->fullUrl(), PHP_URL_PATH), '/');

        return $rootUri === "" || starts_with($fullUri, $rootUri)
            ? trim(substr($fullUri, strlen($rootUri)), '/')
            : $fullUri;
    }
}
