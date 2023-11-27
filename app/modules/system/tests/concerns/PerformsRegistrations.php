<?php namespace October\Tests\Concerns;

use System\Classes\PluginManager;
use Exception;

trait PerformsRegistrations
{
    /**
     * @var array pluginTestCaseLoadedPlugins is a cache for storing
     * which plugins have been loaded.
     */
    protected $pluginTestCaseLoadedPlugins = [];

    /**
     * loadCurrentPlugin
     */
    protected function loadCurrentPlugin()
    {
        // Detect plugin from test
        $pluginCode = $this->guessPluginCodeFromTest();
        $toLoad = [];

        if ($pluginCode !== false) {
            // Load without booting to find dependencies
            $plugin = $this->loadPluginInternal($pluginCode, false);

            // Load dependencies first
            if (!empty($plugin->require)) {
                foreach ((array) $plugin->require as $dependency) {
                    $toLoad[] = $dependency;
                }
            }

            // Load this plugin last
            $toLoad[] = $pluginCode;

            // Load in correct order
            $this->loadPlugins($toLoad);
        }

        return $this->pluginTestCaseLoadedPlugins[$pluginCode] ?? null;
    }

    /**
     * loadAllPlugins found in the system
     */
    protected function loadAllPlugins()
    {
        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Register the plugins to make features like file configuration available
        $pluginManager->registerAll(true);

        // Boot all the plugins to test with dependencies of this plugin
        $pluginManager->bootAll(true);
    }

    /**
     * loadPlugin loads a single plugin
     */
    protected function loadPlugin($code = null)
    {
        if ($code === null) {
            $code = $this->guessPluginCodeFromTest();
        }

        $this->loadPlugins([$code]);

        return $this->pluginTestCaseLoadedPlugins[$code] ?? null;
    }

    /**
     * loadPlugins loads multiple plugins in their correct order
     */
    protected function loadPlugins($codes)
    {
        $pluginInfo = [];
        foreach ($codes as $code) {
            $cached = isset($this->pluginTestCaseLoadedPlugins[$code]);

            if (!$cached) {
                $plugin = $this->loadPluginInternal($code);
                $this->pluginTestCaseLoadedPlugins[$code] = $plugin;
            }

            // Add to array
            $pluginInfo[] = [$this->pluginTestCaseLoadedPlugins[$code], $code, $cached];
        }

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Register plugins first
        foreach ($pluginInfo as $info)  {
            [$pluginObj, $pluginCode, $fromCache] = $info;

            // Prevent double load
            if (!$fromCache) {
                $pluginManager->registerPlugin($pluginObj, $pluginCode);
            }
        }

        // Boot plugins second
        foreach ($pluginInfo as $info)  {
            [$pluginObj, $pluginCode, $fromCache] = $info;

            // Prevent double load
            if (!$fromCache) {
                $pluginManager->bootPlugin($pluginObj, $pluginCode);
            }
        }
    }

    /**
     * loadPluginInternal
     */
    protected function loadPluginInternal($code, $throwException = true)
    {
        if (!preg_match('/^[\w+]*\.[\w+]*$/', $code)) {
            if (!$throwException) {
                return;
            }
            throw new Exception(sprintf('Invalid plugin code: "%s"', $code));
        }

        $manager = PluginManager::instance();
        $plugin = $manager->findByIdentifier($code);

        // First time seeing this plugin, load it up
        if (!$plugin) {
            $namespace = str_replace('.', '\\', strtolower($code));
            $path = array_get($manager->getPluginNamespaces(), $namespace);

            if (!$path) {
                if (!$throwException) {
                    return;
                }
                throw new Exception(sprintf('Unable to find plugin with code: "%s"', $code));
            }

            // Register manually with the plugin manager
            $plugin = $manager->loadPlugin($namespace, plugins_path($path));
        }

        return $plugin;
    }

    /**
     * getPluginObject
     * @deprecated use loadPlugin()
     */
    protected function getPluginObject($code = null)
    {
        return $this->loadPlugin($code);
    }
}
