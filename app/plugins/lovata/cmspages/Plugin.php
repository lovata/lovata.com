<?php namespace Lovata\CmsPages;

use Event;
use Lovata\CmsPages\Classes\Event\CmsPage\ExtendCmsPageHandler;
use System\Classes\PluginBase;

/**
 * Class Plugin
 * @package Lovata\CmsPages
 * @author  Igor Tverdokhleb, i.tverdokhleb@lovata.com, Lovata sp. z o.o.
 */
class Plugin extends PluginBase
{
    public function pluginDetails(): array
    {
        return [
            'name' => 'lovata.cmspages::lang.plugin.name',
            'description' => 'lovata.cmspages::lang.plugin.description',
            'author' => 'Lovata',
            'icon' => 'icon-file-text-o'
        ];
    }

    public function boot()
    {
        Event::subscribe(ExtendCmsPageHandler::class);
    }
}
