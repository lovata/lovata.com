<?php namespace Backend\VueComponents;

use Backend\Classes\VueComponentBase;

/**
 * Dashboard top-level component
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class Dashboard extends VueComponentBase
{
    protected $require = [
        \Backend\VueComponents\DropdownMenu::class,
        \Backend\VueComponents\Modal::class,
        \Backend\VueComponents\LoadingIndicator::class
    ];

    protected function registerSubcomponents()
    {
        $this->registerSubcomponent('container');
        $this->registerSubcomponent('dashboardSelector');
        $this->registerSubcomponent('intervalSelector');
        $this->registerSubcomponent('report');
        $this->registerSubcomponent('reportRow');
        $this->registerSubcomponent('periodDiff');
        $this->registerSubcomponent('reportWidget');
        $this->registerSubcomponent('widgetStatic');
        $this->registerSubcomponent('widgetIndicator');
        $this->registerSubcomponent('widgetChart');
        $this->registerSubcomponent('widgetTable');
        $this->registerSubcomponent('widgetSectionTitle');
        $this->registerSubcomponent('widgetTextNotice');
        $this->registerSubcomponent('widgetError');
    }

    /**
     * Adds component specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * The default component script and CSS file are loaded automatically.
     * @return void
     */
    protected function loadAssets()
    {
        $this->addJsBundle('/modules/backend/assets/js/ph-icons-list.js');
        $this->addJsBundle('/modules/backend/assets/vendor/chartjs/chart.umd.js');
        $this->addJsBundle('/modules/backend/assets/vendor/chartjs-adapter-moment/chartjs-adapter-moment.min.js');
        $this->addJsBundle('js/dashboard-calendar.js');
        $this->addJsBundle('js/dashboard-sizing.js');
        $this->addJsBundle('js/dashboard-dragging.js');
        $this->addJsBundle('js/dashboard-reordering.js');
        $this->addJsBundle('js/dashboard-datasource.js');
        $this->addJsBundle('js/dashboard-manager.js');
        $this->addJsBundle('js/dashboard-datahelper.js');
        $this->addJsBundle('js/dashboard-widget-manager.js');
        $this->addJsBundle('js/dashboard-inspector-configurator.js');
        $this->addJsBundle('js/widget-base.js');
    }
}
