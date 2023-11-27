<?php namespace Cms\Classes;

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
        return base_path('modules/' . $this->dirName);
    }
}
