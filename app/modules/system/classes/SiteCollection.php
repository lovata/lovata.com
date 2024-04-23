<?php namespace System\Classes;

use Site;
use October\Rain\Database\Collection;

/**
 * SiteCollection is a collection of sites
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class SiteCollection extends Collection
{
    /**
     * isPrimary
     */
    public function isPrimary()
    {
        return $this->where('is_primary', true);
    }

    /**
     * isEnabled
     */
    public function isEnabled()
    {
        return $this->where('is_enabled', true);
    }

    /**
     * isEnabledEdit
     */
    public function isEnabledEdit()
    {
        return $this->where('is_enabled_edit', true);
    }

    /**
     * inSiteGroup
     */
    public function inSiteGroup($siteId = null)
    {
        $site = $siteId ? Site::getSiteFromId($siteId) : Site::getSiteFromContext();

        if ($groupId = $site?->group_id) {
            return $this->where('group_id', $groupId);
        }

        return $this;
    }

    /**
     * inSiteLocale
     */
    public function inSiteLocale($siteId = null)
    {
        $site = $siteId ? Site::getSiteFromId($siteId) : Site::getSiteFromContext();

        if ($localeCode = $site?->hard_locale) {
            return $this->where('locale', $localeCode);
        }

        return new static;
    }
}
