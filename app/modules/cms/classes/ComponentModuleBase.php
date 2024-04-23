<?php namespace Cms\Classes;

use Config;

/**
 * ComponentModuleBase is an internal base class used by module components
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class ComponentModuleBase extends ComponentBase
{
    /**
     * getPath returns the absolute component path
     */
    public function getPath()
    {
        if ($this->componentGetPathCache !== null) {
            return $this->componentGetPathCache;
        }

        return $this->componentGetPathCache = base_path('modules/' . $this->dirName);
    }

    /**
     * getComponentAssetUrlPath returns the public directory for the component assets
     */
    protected function getComponentAssetUrlPath(): string
    {
        // Configuration for component asset location, default to relative path
        $assetUrl = '/modules';
        if ($customUrl = Config::get('app.asset_url')) {
            $assetUrl = $customUrl . $assetUrl;
        }

        // Build path
        $dirName = dirname(dirname($this->dirName));

        return $assetUrl . '/' . $dirName;
    }
}
