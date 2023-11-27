<?php namespace Lovata\MightySeo;

use Event;
use System\Classes\PluginBase;

use Lovata\MightySeo\Classes\TemplateProcessor;
use Lovata\MightySeo\Classes\Item\SeoTemplateItem;
use Lovata\MightySeo\Classes\Event\ExtendModel;
use Lovata\MightySeo\Classes\Event\ExtendFieldHandler;
use Lovata\MightySeo\Classes\Event\SeoParamModelHandler;
use Lovata\MightySeo\Classes\Event\SeoTemplateModelHandler;

/**
 * Class Plugin
 * @package Lovata\MightySeo
 * @author Andrey Kharanenka, a.khoronenko@LOVATA.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    public $require = ['Lovata.Toolbox'];

    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\MightySeo\Components\SeoToolbox' => 'SeoToolbox',
        ];
    }

    /**
     * Plugin boot method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    /**
     * Add event listeners
     */
    protected function addEventListener()
    {
        Event::subscribe(ExtendModel::class);
        Event::subscribe(SeoTemplateModelHandler::class);
        Event::subscribe(SeoParamModelHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
    }

    /**
     * Register twig functions and filters
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'seo_template' => function($sKey, $arParamList = [], $bOneLine = false) {
                    //Get SEO template item
                    $obTemplateItem = SeoTemplateItem::make($sKey);
                    /** @var TemplateProcessor $obTemplateProcessor */
                    $obTemplateProcessor = $this->app->make(TemplateProcessor::class, [$obTemplateItem->value, $arParamList, $bOneLine]);

                    return $obTemplateProcessor->run();
                },
            ]
        ];
    }

    /**
     * @return array
     */
    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'lovata.mightyseo::lang.menu.settings',
                'description' => 'lovata.mightyseo::lang.menu.settings_description',
                'icon'        => 'oc-icon-line-chart',
                'class'       => 'Lovata\MightySeo\Models\Settings',
                'permissions' => ['mighty-seo'],
                'order'       => 200,
            ]
        ];
    }
}
