<?php namespace System\Classes\UpdateManager;

use System;
use October\Rain\Composer\Manager as ComposerManager;
use Cms\Classes\Theme as CmsTheme;
use ApplicationException;

/**
 * ManagesThemes
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
trait ManagesThemes
{
    /**
     * installTheme using composer
     */
    public function installTheme($name)
    {
        [$package, $version] = $this->findThemeComposerCode($name);
        if (!$package) {
            throw new ApplicationException("Package [$name] not found");
        }

        $composer = ComposerManager::instance();
        $composer->require([$package => $this->getComposerVersionConstraint($version)]);

        // Lock theme
        $themeFolder = strtolower(str_replace('.', '-', System::composerToOctoberCode($package)));
        $this->themeManager->createChildTheme($themeFolder);
        $this->themeManager->performLockOnTheme($themeFolder);
    }

    /**
     * findThemeComposerCode locates a composer code for a plugin
     */
    protected function findThemeComposerCode(string $code): array
    {
        // Local
        if ($this->themeManager->findByIdentifier($code)) {
            $composerCode = $this->themeManager->getComposerCode($code);
            $composerVersion = $this->themeManager->getLatestVersion($code);
        }
        // Remote
        else {
            $details = $this->requestThemeDetails($code);
            $composerCode = $details['composer_code'] ?? '';
            $composerVersion = $details['composer_version'] ?? '';
        }

        return [$composerCode, $composerVersion];
    }

    /**
     * uninstallTheme attempts to remove the theme using composer before
     * deleting from the filesystem
     */
    public function uninstallTheme($name)
    {
        $themeExists = CmsTheme::exists($name);
        if (!$themeExists) {
            $name = (string) $this->themeManager->findDirectoryName($name);
        }

        if (!CmsTheme::exists($name)) {
            throw new ApplicationException("Theme [$name] not found");
        }

        // Remove via composer
        if ($composerCode = $this->themeManager->getComposerCode($name)) {
            $composer = ComposerManager::instance();
            $composer->remove([$composerCode]);
        }

        $this->themeManager->deleteTheme($name);
    }

    /**
     * requestThemeDetails looks up a theme from the update server
     */
    public function requestThemeDetails(string $name): array
    {
        return $this->requestServerData('package/detail', ['name' => $name, 'type' => 'theme']);
    }

    /**
     * requestThemeContent looks up content for a theme from the update server
     */
    public function requestThemeContent(string $name): array
    {
        return $this->requestServerData('package/content', ['name' => $name, 'type' => 'theme']);
    }
}
