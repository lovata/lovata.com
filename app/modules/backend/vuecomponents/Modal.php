<?php namespace Backend\VueComponents;

use Backend\Classes\VueComponentBase;

/**
 * Modal dialog Vue component.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Modal extends VueComponentBase
{
    /**
     * @var string assetDefaults is the default attributes for assets.
     */
    protected $assetDefaults = ['build' => 'global'];

    /**
     * loadAssets adds component specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page. The default component script and CSS
     * file are loaded automatically.
     */
    protected function loadAssets()
    {
        $this->addJsBundle('js/modal-position.js');
        $this->addJsBundle('js/modal-size.js');
        $this->addJsBundle('js/modal-utils.js');
    }

    /**
     * registerSubcomponents
     */
    protected function registerSubcomponents()
    {
        $this->registerSubcomponent('alert');
        $this->registerSubcomponent('confirm');
    }
}
