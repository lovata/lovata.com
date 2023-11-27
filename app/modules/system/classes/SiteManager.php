<?php namespace System\Classes;

use App;
use Manifest;
use System\Models\SiteDefinition;
use October\Rain\Database\Collection;
use Exception;

/**
 * SiteManager class manages sites
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class SiteManager
{
    use \System\Classes\SiteManager\HasEditSite;
    use \System\Classes\SiteManager\HasActiveSite;
    use \System\Classes\SiteManager\HasSiteContext;
    use \System\Classes\SiteManager\HasPreferredLanguage;

    /**
     * @var string keys for manifest storage
     */
    const MANIFEST_SITES = 'sites.all';

    /**
     * @var array sites collection of sites
     */
    protected $sites;

    /**
     * @var array siteIdCache caches sites by their identifier
     */
    protected $siteIdCache = [];

    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('system.sites');
    }

    /**
     * getSiteFromRequest locates the site based on the hostname and URI
     */
    public function getSiteFromRequest(string $host, string $uri)
    {
        $sites = $this->listSites()
            ->where('is_enabled', true)
            ->filter(function($site) use ($host) {
                return $site->matchesHostname($host);
            })
            ->filter(function($site) use ($uri) {
                return $site->matchesRoutePrefix($uri);
            })
        ;

        // With multiples, try to target custom URL
        if ($sites->count() > 1) {
            $sites = $sites->filter(function($site) use ($host) {
                return $site->matchesBaseUrl($host);
            });
        }

        // With multiples, handle prefix collisions
        if ($sites->count() > 1) {
            $sites = $sites->sortByDesc(function($site) {
                return $site->is_prefixed ? $site->route_prefix : '';
            });
        }

        return $sites->first() ?: $this->getPrimarySite();
    }

    /**
     * getSiteFromId
     */
    public function getSiteFromId($id)
    {
        if (isset($this->siteIdCache[$id])) {
            return $this->siteIdCache[$id];
        }

        return $this->siteIdCache[$id] = $this->listSites()->find($id);
    }

    /**
     * getPrimarySite
     */
    public function getPrimarySite()
    {
        return $this->listSites()->where('is_primary', true)->first();
    }

    /**
     * getAnySite returns any site, with priority to primary
     */
    public function getAnySite()
    {
        return $this->getPrimarySite() ?: $this->listEnabled()->first();
    }

    /**
     * hasAnySite returns true if there is a frontend
     */
    public function hasAnySite(): bool
    {
        return $this->listSites()->where('is_enabled', true)->count() > 0;
    }

    /**
     * hasMultiSite returns true if there are multiple sites
     */
    public function hasMultiSite(): bool
    {
        return $this->listSites()->where('is_enabled', true)->count() > 1;
    }

    /**
     * listEnabled
     */
    public function listEnabled()
    {
        return $this->listSites()->where('is_enabled', true);
    }

    /**
     * listSiteIds
     */
    public function listSiteIds()
    {
        return $this->listSites()->pluck('id')->all();
    }

    /**
     * listSites
     */
    public function listSites()
    {
        if ($this->sites !== null) {
            return $this->sites;
        }

        if (Manifest::has(self::MANIFEST_SITES)) {
            $this->sites = $this->listSitesFromManifest(
                (array) Manifest::get(self::MANIFEST_SITES)
            );
        }
        else {
            try {
                $this->sites = SiteDefinition::all();
            }
            catch (Exception $ex) {
                return new Collection([SiteDefinition::makeFallbackInstance()]);
            }

            Manifest::put(
                self::MANIFEST_SITES,
                $this->listSitesForManifest($this->sites)
            );
        }

        return $this->sites;
    }

    /**
     * listSitesFromManifest
     */
    protected function listSitesFromManifest($sites)
    {
        $items = [];
        foreach ($sites as $attributes) {
            $site = new SiteDefinition;
            $site->attributes = $attributes;
            $site->syncOriginal();
            $items[] = $site;
        }
        return new Collection($items);
    }

    /**
     * listSitesForManifest
     */
    protected function listSitesForManifest($sites)
    {
        $items = [];
        foreach ($sites as $site) {
            $items[] = $site->attributes;
        }
        return $items;
    }

    /**
     * resetCache resets any memory or cache involved with the sites
     */
    public function resetCache()
    {
        $this->sites = null;
        $this->siteIdCache = [];
        Manifest::forget(self::MANIFEST_SITES);
    }
}
