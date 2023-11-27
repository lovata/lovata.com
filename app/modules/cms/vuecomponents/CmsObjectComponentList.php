<?php namespace Cms\VueComponents;

use Backend\Classes\VueComponentBase;

/**
 * CmsObjectComponentList is a Vue component
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class CmsObjectComponentList extends VueComponentBase
{
    /**
     * registerSubcomponents
     */
    protected function registerSubcomponents()
    {
        $this->registerSubcomponent('component');
    }
}
