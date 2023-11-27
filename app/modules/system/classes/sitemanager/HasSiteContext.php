<?php namespace System\Classes\SiteManager;

use App;
use System\Models\SiteDefinition;
use Closure;

/**
 * HasSiteContext
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasSiteContext
{
    /**
     * @var bool globalContext disables site filters globally.
     */
    protected $globalContext = false;

    /**
     * @var SiteDefinition|null siteContext overrides the current site context.
     */
    protected $siteContext = null;

    /**
     * getSiteIdFromContext
     * @return int|null
     */
    public function getSiteIdFromContext()
    {
        $site = $this->getSiteFromContext();

        if (!$site || !$site->id) {
            return null;
        }

        return (int) $site->id;
    }

    /**
     * getSiteFromContext
     * @return SiteDefinition
     */
    public function getSiteFromContext()
    {
        if ($this->siteContext !== null) {
            return $this->siteContext;
        }

        return App::runningInBackend()
            ? $this->getEditSite()
            : $this->getActiveSite();
    }

    /**
     * hasGlobalContext
     */
    public function hasGlobalContext(): bool
    {
        return $this->globalContext;
    }

    /**
     * withGlobalContext
     */
    public function withGlobalContext(Closure $callback)
    {
        $previous = $this->globalContext;

        $this->globalContext = true;

        try {
            return $callback();
        }
        finally {
            $this->globalContext = $previous;
        }
    }

    /**
     * withContext
     */
    public function withContext($siteId, Closure $callback)
    {
        $previous = $this->siteContext;

        $this->siteContext = $this->getSiteFromId($siteId);

        try {
            return $callback();
        }
        finally {
            $this->siteContext = $previous;
        }
    }
}
