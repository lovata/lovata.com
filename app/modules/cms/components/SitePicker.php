<?php namespace Cms\Components;

use Cms;
use Site;
use Cms\Classes\Page;
use Cms\Classes\ComponentModuleBase;
use Exception;

/**
 * SitePicker component
 */
class SitePicker extends ComponentModuleBase
{
    /**
     * @var array sitesCache for multiple calls
     */
    protected $sitesCache;

    /**
     * componentDetails
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name' => 'Site Picker',
            'description' => 'Displays links for selecting a different site.',
        ];
    }

    /**
     * isEnabled returns true if the site picker should be displayed
     */
    public function isEnabled()
    {
        return Site::hasMultiSite();
    }

    /**
     * sites lazily loads the available sites
     */
    public function sites()
    {
        if ($this->sitesCache !== null) {
            return $this->sitesCache;
        }

        $sites = Site::listEnabled();

        foreach ($sites as $site) {
            $site->setUrlOverride(Cms::siteUrl(
                $this->getPage(),
                $site,
                $this->getRouter()->getParameters()
            ));
        }

        return $this->sitesCache = $sites;
    }

    /**
     * pageSites
     */
    public function pageSites($pageName, $params = [])
    {
        try {
            $page = Page::loadCached($this->getTheme(), $pageName);
            if (!$page) {
                return [];
            }
        }
        catch (Exception $ex) {
            return [];
        }

        $sites = Site::listEnabled();

        foreach ($sites as $site) {
            $site->setUrlOverride(Cms::siteUrl($page, $site, (array) $params));
        }

        return $sites;
    }
}
