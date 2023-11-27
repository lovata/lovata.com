<?php namespace Backend\Classes\WidgetManager;

use App;
use Event;
use System;
use BackendAuth;
use SystemException;

/**
 * HasReportWidgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasReportWidgets
{
    /**
     * @var array reportWidgets
     */
    protected $reportWidgets;

    /**
     * @var array reportWidgetCallbacks cache
     */
    protected $reportWidgetCallbacks = [];

    /**
     * listReportWidgets returns a list of registered report widgets.
     * @return array Array keys are class names.
     */
    public function listReportWidgets()
    {
        if ($this->reportWidgets === null) {
            $this->reportWidgets = [];

            // Load external widgets
            foreach ($this->reportWidgetCallbacks as $callback) {
                $callback($this);
            }

            // Load module items
            foreach (System::listModules() as $module) {
                if ($provider = App::getProvider($module . '\\ServiceProvider')) {
                    $widgets = $provider->registerReportWidgets();
                    if (is_array($widgets)) {
                        foreach ($widgets as $className => $widgetInfo) {
                            $this->registerReportWidget($className, $widgetInfo);
                        }
                    }
                }
            }

            // Load plugin widgets
            foreach ($this->pluginManager->getPlugins() as $plugin) {
                $widgets = $plugin->registerReportWidgets();
                if (!is_array($widgets)) {
                    continue;
                }

                foreach ($widgets as $className => $widgetInfo) {
                    $this->registerReportWidget($className, $widgetInfo);
                }
            }

            // Load app widgets
            if ($app = App::getProvider(\App\Provider::class)) {
                $widgets = $app->registerReportWidgets();
                if (is_array($widgets)) {
                    foreach ($widgets as $className => $widgetInfo) {
                        $this->registerReportWidget($className, $widgetInfo);
                    }
                }
            }
        }

        /**
         * @event system.reportwidgets.extendItems
         * Enables adding or removing report widgets.
         *
         * You will have access to the WidgetManager instance and be able to call the appropriate methods
         * $manager->registerReportWidget();
         * $manager->removeReportWidget();
         *
         * Example usage:
         *
         *     Event::listen('system.reportwidgets.extendItems', function ($manager) {
         *          $manager->removeReportWidget('Acme\ReportWidgets\YourWidget');
         *     });
         *
         */
        Event::fire('system.reportwidgets.extendItems', [$this]);

        $user = BackendAuth::getUser();
        foreach ($this->reportWidgets as $widget => $config) {
            if (!empty($config['permissions'])) {
                if (!$user->hasAccess($config['permissions'], false)) {
                    unset($this->reportWidgets[$widget]);
                }
            }
        }

        return $this->reportWidgets;
    }

    /**
     * getReportWidgets returns the raw array of registered report widgets.
     * @return array Array keys are class names.
     */
    public function getReportWidgets()
    {
        return $this->reportWidgets;
    }

    /*
     * registerReportWidget registers a single report widget.
     */
    public function registerReportWidget($className, $widgetInfo)
    {
        $this->reportWidgets[$className] = $widgetInfo;
    }

    /**
     * registerReportWidgets manually registers report widget for consideration. Usage:
     *
     *     WidgetManager::registerReportWidgets(function ($manager) {
     *         $manager->registerReportWidget(\RainLab\GoogleAnalytics\ReportWidgets\TrafficOverview::class, [
     *             'name' => 'Google Analytics traffic overview',
     *             'context' => 'dashboard'
     *         ]);
     *     });
     *
     */
    public function registerReportWidgets(callable $definitions)
    {
        $this->reportWidgetCallbacks[] = $definitions;
    }

    /**
     * removeReportWidget removes a registered ReportWidget.
     * @param string $className Widget class name.
     * @return void
     */
    public function removeReportWidget($className)
    {
        if (!$this->reportWidgets) {
            throw new SystemException('Unable to remove a widget before widgets are loaded.');
        }

        unset($this->reportWidgets[$className]);
    }
}
