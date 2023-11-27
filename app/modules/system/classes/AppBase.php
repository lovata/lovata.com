<?php namespace System\Classes;

use App;
use October\Rain\Support\ModuleServiceProvider;

/**
 * AppBase class is an application level plugin
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class AppBase extends ModuleServiceProvider
{
    /**
     * register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $appPath = app_path();
        $appNamespace = 'app';

        // Register configuration path
        $configPath = $appPath . '/config';
        if (is_dir($configPath)) {
            $this->loadConfigFrom($configPath, $appNamespace);
        }

        // Register views path
        $viewsPath = $appPath . '/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $appNamespace);
        }

        // Add init, if available
        $initFile = $appPath . '/init.php';
        if (file_exists($initFile)) {
            require $initFile;
        }

        // Add routes, if available
        $routesFile = $appPath . '/routes.php';
        if (!App::routesAreCached() && file_exists($routesFile)) {
            require $routesFile;
        }
    }

    /**
     * boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
    }
}
