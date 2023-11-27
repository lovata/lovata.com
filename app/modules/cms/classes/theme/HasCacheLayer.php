<?php namespace Cms\Classes\Theme;

use File;

/**
 * HasCacheLayer for themes
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasCacheLayer
{
    /**
     * getCachedThemePath
     */
    public function getCachedThemePath(): string
    {
        if (!$this->dirName) {
            return cache_path("cms/theme.php");
        }

        return cache_path("cms/theme-{$this->dirName}.php");
    }

    /**
     * themeIsCached
     */
    public function themeIsCached(): bool
    {
        return File::exists($this->getCachedThemePath());
    }
}
