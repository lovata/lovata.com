<?php namespace Tailor\VueComponents;

use Backend\Classes\VueComponentBase;

/**
 * PublishingControls for Tailor entry as a Vue component.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class PublishingControls extends VueComponentBase
{
    /**
     * @var array require
     */
    protected $require = [
        \Backend\VueComponents\Popover::class
    ];

    /**
     * loadDependencyAssets adds dependency assets required for the component.
     * This method is called before the component's default resources are loaded.
     * Use $this->addJs() and $this->addCss() to register new assets to include
     * on the page.
     * @return void
     */
    protected function loadDependencyAssets()
    {
        $this->addJsBundle('js/domtools.js');
    }
}
