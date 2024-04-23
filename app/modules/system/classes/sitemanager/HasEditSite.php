<?php namespace System\Classes\SiteManager;

use Cms;
use Event;
use Config;
use BackendAuth;
use System\Models\SiteDefinition;

/**
 * HasEditSite
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasEditSite
{
    /**
     * @var mixed editSiteCache
     */
    protected $editSiteCache;

    /**
     * getEditSite returns the edit theme
     */
    public function getEditSite()
    {
        if ($this->editSiteCache !== null) {
            return $this->editSiteCache;
        }

        $editSite = $this->getSiteFromId($this->getEditSiteId());

        if (!$editSite || !$editSite->matchesRole(BackendAuth::getUser())) {
            $editSite = $this->getAnyEditSite();
        }

        return $this->editSiteCache = $editSite;
    }

    /**
     * getEditSiteId
     */
    public function getEditSiteId()
    {
        return Config::get('system.edit_site');
    }

    /**
     * setEditSiteId
     */
    public function setEditSiteId($id)
    {
        $this->editSiteCache = null;

        Config::set('system.edit_site', $id);

        /**
         * @event system.site.setEditSite
         * Fires when the edit site has been changed.
         *
         * Example usage:
         *
         *     Event::listen('system.site.setEditSite', function($id) {
         *         \Log::info("Site has been changed to $id");
         *     });
         *
         */
        Event::fire('system.site.setEditSite', [$id]);

        $this->broadcastSiteChange($id);
    }

    /**
     * setEditSite
     */
    public function setEditSite($site)
    {
        $this->setEditSiteId($site->id);
    }

    /**
     * hasAnyEditSite returns true if there are edit sites
     */
    public function getAnyEditSite()
    {
        return $this->listEditEnabled()->isPrimary()->first()
            ?: $this->listEditEnabled()->first();
    }

    /**
     * hasAnyEditSite returns true if there are edit sites
     */
    public function hasAnyEditSite(): bool
    {
        return $this->listEditSites()->isEnabledEdit()->count() > 0;
    }

    /**
     * hasMultiEditSite returns true if there are multiple sites for editing
     */
    public function hasMultiEditSite(): bool
    {
        return $this->listEditSites()->isEnabledEdit()->count() > 1;
    }

    /**
     * listEditEnabled
     */
    public function listEditEnabled()
    {
        return $this->listEditSites()->isEnabledEdit();
    }

    /**
     * listEditSites
     */
    public function listEditSites()
    {
        return $this->listSites()->filter(function($site) {
            return $site->matchesRole(BackendAuth::getUser());
        });
    }

    /**
     * applyEditSite applies edit site configuration values to the application,
     * typically used for backend requests.
     */
    public function applyEditSite(SiteDefinition $site)
    {
        if ($site->theme) {
            Config::set('cms.edit_theme', $site->theme);
        }

        if ($site->is_prefixed) {
            Cms::setUrlPrefix($site->route_prefix);
        }
    }
}
