<?php namespace System\Classes\UpdateManager;

use Lang;
use October\Rain\Composer\Manager as ComposerManager;
use ApplicationException;

/**
 * ManagesPlugins
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait ManagesPlugins
{
    /**
     * installPlugin using composer
     */
    public function installPlugin($name)
    {
        [$package, $version] = $this->findPluginComposerCode($name);
        if (!$package) {
            throw new ApplicationException("Package [$name] not found");
        }

        $composer = ComposerManager::instance();
        $composer->require([$package => $this->getComposerVersionConstraint($version)]);
    }

    /**
     * findPluginComposerCode locates a composer code for a plugin
     */
    protected function findPluginComposerCode(string $code): array
    {
        // Local
        if ($plugin = $this->pluginManager->findByIdentifier($code)) {
            $composerCode = $this->pluginManager->getComposerCode($plugin);
            $composerVersion = $this->versionManager->getLatestVersion($code);
        }
        // Remote
        else {
            $details = $this->requestPluginDetails($code);
            $composerCode = $details['composer_code'] ?? '';
            $composerVersion = $details['composer_version'] ?? '';
        }

        return [$composerCode, $composerVersion];
    }

    /**
     * uninstallPlugin attempts to remove the plugin using composer before
     * deleting from the filesystem
     */
    public function uninstallPlugin($name)
    {
        if (!$this->pluginManager->hasPlugin($name)) {
            throw new ApplicationException("Plugin [$name] not found");
        }

        // Remove via composer
        $composer = ComposerManager::instance();
        $composerCode = $this->pluginManager->getComposerCode($name);

        if ($composerCode && $composer->hasPackage($composerCode)) {
            $this->rollbackPlugin($name);
            $composer->remove([$composerCode]);
        }

        $this->pluginManager->deletePlugin($name);
    }

    /**
     * requestPluginDetails looks up a plugin from the update server
     */
    public function requestPluginDetails(string $name): array
    {
        return $this->requestServerData('package/detail', ['name' => $name, 'type' => 'plugin']);
    }

    /**
     * requestPluginContent looks up content for a plugin from the update server
     */
    public function requestPluginContent(string $name): array
    {
        return $this->requestServerData('package/content', ['name' => $name, 'type' => 'plugin']);
    }

    /**
     * migratePlugins migrates all plugins
     */
    public function migratePlugins()
    {
        $plugins = $this->pluginManager->getPlugins();
        foreach ($plugins as $code => $plugin) {
            $this->migratePlugin($code);
        }
    }

    /**
     * migratePlugin runs update on a single plugin
     */
    public function migratePlugin(string $name)
    {
        // Update the plugin database and version
        $plugin = $this->pluginManager->findByIdentifier($name);

        if (!$plugin) {
            $this->note('<error>Unable to find</error> ' . $name);
            return;
        }

        $this->versionManager->setNotesOutput($this->notesOutput);

        if ($this->versionManager->updatePlugin($plugin)) {
            $this->migrateCount++;
        }
    }

    /**
     * updatePlugin
     * @deprecated use migratePlugin
     */
    public function updatePlugin(string $name)
    {
        $this->migratePlugin($name);
    }

    /**
     * rollbackPlugin removes an existing plugin database and version record
     */
    public function rollbackPlugin(string $name)
    {
        $plugin = $this->pluginManager->findByIdentifier($name);

        if (!$plugin && $this->versionManager->purgePlugin($name)) {
            $this->note('<info>Purged from database</info> ' . $name);
            return;
        }

        if ($this->versionManager->removePlugin($plugin)) {
            $this->note('<info>Rolled back</info> ' . $name);
            return;
        }

        $this->note('<error>Unable to find</error> ' . $name);
    }

    /**
     * rollbackPlugin removes an existing plugin database and version record
     */
    public function rollbackPluginToVersion(string $name, string $toVersion)
    {
        $toVersion = ltrim($toVersion, 'v');

        $plugin = $this->pluginManager->findByIdentifier($name);

        if (!$plugin && $this->versionManager->purgePlugin($name)) {
            $this->note('<info>Purged from database</info> ' . $name);
            return;
        }

        if (!$this->versionManager->hasVersion($plugin, $toVersion)) {
            throw new ApplicationException(Lang::get('system::lang.updates.plugin_version_not_found'));
        }

        if ($this->versionManager->removePluginToVersion($plugin, $toVersion)) {
            $this->note("<info>Rolled back</info> {$name} <info>to version</info> {$toVersion}");
            return;
        }

        $this->note('<error>Unable to find</error> ' . $name);
    }
}
