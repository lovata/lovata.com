<?php namespace Backend\Classes;

use App;
use System\Classes\PluginManager;

/**
 * WidgetManager
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class WidgetManager
{
    use \Backend\Classes\WidgetManager\HasFormWidgets;
    use \Backend\Classes\WidgetManager\HasFilterWidgets;
    use \Backend\Classes\WidgetManager\HasReportWidgets;

    /**
     * @var \System\Classes\PluginManager pluginManager
     */
    protected $pluginManager;

    /**
     * __construct this class
     */
    public function __construct()
    {
        $this->pluginManager = PluginManager::instance();
    }

    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('backend.widgets');
    }
}
