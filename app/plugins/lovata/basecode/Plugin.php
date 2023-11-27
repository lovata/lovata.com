<?php namespace Lovata\BaseCode;

use Lovata\Basecode\Console\MigrateMagicForms;
use Lovata\Basecode\Console\RemoveOldPluginsData;
use October\Rain\Argon\Argon;
use System\Classes\PluginBase;
use Event;
// Article events
use Lovata\BaseCode\Classes\Event\Article\ExtendArticleFieldsHandler;
use Lovata\BaseCode\Classes\Event\Article\ArticleModelHandler;
// Project events
use Lovata\BaseCode\Classes\Event\Project\ProjectModelHandler;
// CMS Page event
use Lovata\BaseCode\Classes\Event\CmsPage\ExtendCmsPageHandler;

/**
 * Class Plugin
 * @package Lovata\BaseCode
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    const CACHE_TAG = 'base-code';

    /** @var array Plugin dependencies */
    public $require = [
        'Lovata.GoodNews',
    ];


    public function register()
    {
        $this->registerConsoleCommand('basecode:removeoldpluginsdata', RemoveOldPluginsData::class);
        $this->registerConsoleCommand('basecode:migratemagicforms', MigrateMagicForms::class);
    }

    /**
     * Register twig functions
     * @return array
     */
//    public function registerMarkupTags()
//    {
//        return [
//            'filters' => [
//                'argon' => function ($sTime, $sFormat) {
//                    if (empty($sTime) || empty($sFormat)) {
//                        return null;
//                    }
//
//                    $sTime = Argon::parse($sTime)->format($sFormat);
//                    return $sTime;
//                },
//            ]
//        ];
//    }

    /**
     * @return array|void
     */
    public function boot()
    {
//        $this->addEventListener();
    }

    /**
     * Add listeners
     */
    protected function addEventListener()
    {
        // Article event
        Event::subscribe(ExtendArticleFieldsHandler::class);
        Event::subscribe(ArticleModelHandler::class);
        // Project event
        Event::subscribe(ProjectModelHandler::class);
        // CMS Page event
        Event::subscribe(ExtendCmsPageHandler::class);
    }

    /**
     * Registration components
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\BaseCode\Components\ProjectList' => 'ProjectList',
            'Lovata\BaseCode\Components\ProjectPage' => 'ProjectPage',
        ];
    }
}
