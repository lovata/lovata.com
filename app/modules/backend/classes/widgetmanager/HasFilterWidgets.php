<?php namespace Backend\Classes\WidgetManager;

use App;
use Str;
use System;

/**
 * HasFilterWidgets
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasFilterWidgets
{
    /**
     * @var array filterWidgets
     */
    protected $filterWidgets;

    /**
     * @var array filterWidgetCallbacks cache
     */
    protected $filterWidgetCallbacks = [];

    /**
     * @var array filterWidgetHints keyed by their code.
     * Stored in the form of ['filterwidgetcode' => 'FilterWidgetClass'].
     */
    protected $filterWidgetHints;

    /**
     * listFilterWidgets returns a list of registered filter widgets.
     * @return array Array keys are class names.
     */
    public function listFilterWidgets()
    {
        if ($this->filterWidgets === null) {
            $this->filterWidgets = [];

            // Load external widgets
            foreach ($this->filterWidgetCallbacks as $callback) {
                $callback($this);
            }

            // Load module items
            foreach (System::listModules() as $module) {
                if ($provider = App::getProvider($module . '\\ServiceProvider')) {
                    $widgets = $provider->registerFilterWidgets();
                    if (is_array($widgets)) {
                        foreach ($widgets as $className => $widgetInfo) {
                            $this->registerFilterWidget($className, $widgetInfo);
                        }
                    }
                }
            }

            // Load plugin widgets
            foreach ($this->pluginManager->getPlugins() as $plugin) {
                $widgets = $plugin->registerFilterWidgets();
                if (!is_array($widgets)) {
                    continue;
                }

                foreach ($widgets as $className => $widgetInfo) {
                    $this->registerFilterWidget($className, $widgetInfo);
                }
            }

            // Load app widgets
            if ($app = App::getProvider(\App\Provider::class)) {
                $widgets = $app->registerFilterWidgets();
                if (is_array($widgets)) {
                    foreach ($widgets as $className => $widgetInfo) {
                        $this->registerFilterWidget($className, $widgetInfo);
                    }
                }
            }
        }

        return $this->filterWidgets;
    }

    /**
     * getFilterWidgets returns the raw array of registered filter widgets.
     * @return array Array keys are class names.
     */
    public function getFilterWidgets()
    {
        return $this->filterWidgets;
    }

    /*
     * registerFilterWidget registers a single filter widget.
     */
    public function registerFilterWidget($className, $widgetInfo)
    {
        if (!is_array($widgetInfo)) {
            $widgetInfo = ['code' => $widgetInfo];
        }

        $widgetCode = $widgetInfo['code'] ?? null;

        if (!$widgetCode) {
            $widgetCode = Str::getClassId($className);
        }

        $this->filterWidgets[$className] = $widgetInfo;
        $this->filterWidgetHints[$widgetCode] = $className;
    }

    /**
     * registerFilterWidgets manually registers filter widget for consideration. Usage:
     *
     *     WidgetManager::registerFilterWidgets(function ($manager) {
     *         $manager->registerFilterWidget(\Backend\FilterWidgets\DateRange::class, 'daterange');
     *     });
     *
     */
    public function registerFilterWidgets(callable $definitions)
    {
        $this->filterWidgetCallbacks[] = $definitions;
    }

    /**
     * resolveFilterWidget returns a class name from a filter widget code
     * Normalizes a class name or converts an code to its class name.
     * @param string $name Class name or form widget code.
     * @return string The class name resolved, or the original name.
     */
    public function resolveFilterWidget($name)
    {
        if ($this->filterWidgets === null) {
            $this->listFilterWidgets();
        }

        $hints = $this->filterWidgetHints;

        if (isset($hints[$name])) {
            return $hints[$name];
        }

        $_name = Str::normalizeClassName($name);
        if (isset($this->filterWidgets[$_name])) {
            return $_name;
        }

        return $name;
    }
}
