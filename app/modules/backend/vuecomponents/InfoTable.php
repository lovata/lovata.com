<?php namespace Backend\VueComponents;

use Backend\Classes\VueComponentBase;

/**
 * InfoTable is a read-only information table Vue component
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class InfoTable extends VueComponentBase
{
    /**
     * registerSubcomponents
     */
    protected function registerSubcomponents()
    {
        $this->registerSubcomponent('item');
    }
}
