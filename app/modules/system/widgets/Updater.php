<?php namespace System\Widgets;

use Log;
use Flash;
use System;
use Backend;
use Ui;
use Redirect;
use Cms\Classes\ThemeManager;
use System\Classes\UpdateManager;
use System\Models\PluginVersion;
use System\Classes\PluginManager;
use October\Rain\Composer\Manager as ComposerManager;
use Backend\Classes\WidgetBase;
use System\Helpers\Cache as CacheHelper;
use ApplicationException;
use AjaxException;
use Exception;

/**
 * Updater widget
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Updater extends WidgetBase
{
    /**
     * @var string Defined alias used for this widget.
     */
    public $alias = 'updater';

    /**
     * loadAssets adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     */
    protected function loadAssets()
    {
        $this->addCss('css/updater.css');
        $this->addJs('js/updater.js');
    }

    /**
     * render renders the widget
     */
    public function render(): string
    {
        return '';
    }

    /**
     * renderWarnings warnings provided by the updater
     */
    public function renderWarnings(): string
    {
        try {
            $deps = PluginManager::instance()->findMissingDependencies();

            if (System::hasModule('Cms')) {
                $themeDeps = ThemeManager::instance()->findMissingDependencies();
                $deps = array_unique(array_merge($deps, $themeDeps));
            }
        }
        catch (Exception $ex) {
            return '';
        }

        if (!$deps) {
            return '';
        }

        return (string) Ui::callout()
            ->danger()
            ->label(__('There are missing dependencies needed for the system to run correctly.'))
            ->action(
                Ui::popupButton(__('Check Dependencies'), $this->getEventHandler('onCheckDependencies'))
                    ->danger()
                    ->loading()
            );
    }

    /**
     * onCheckDependencies for the system
     */
    public function onCheckDependencies()
    {
        $deps = PluginManager::instance()->findMissingDependencies();

        if (System::hasModule('Cms')) {
            $themeDeps = ThemeManager::instance()->findMissingDependencies();
            $deps = array_unique(array_merge($deps, $themeDeps));
        }

        return $this->makePartial('require_form', ['deps' => $deps]);
    }

    /**
     * onInstallDependencies for the system
     */
    public function onInstallDependencies()
    {
        try {
            $deps = (array) post('deps', []);
            $updateSteps = [];

            foreach ($deps as $code) {
                $updateSteps[] = [
                    'code' => 'installPlugin',
                    'label' => __('Installing plugin: :name', ['name' => $code]),
                    'name' => $code
                ];
            }

            $updateSteps[] = [
                'code' => 'completeUpdate',
                'label' => __('Finishing update process'),
                'type' => 'final'
            ];

            $this->vars['updateSteps'] = $updateSteps;
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('execute');
    }

    /**
     * onLoadUpdates spawns the update checker popup
     */
    public function onLoadUpdates()
    {
        return $this->makePartial('update_form');
    }

    /**
     * onCheckForUpdates contacts the update server for a list of necessary updates.
     */
    public function onCheckForUpdates()
    {
        try {
            $result = UpdateManager::instance()->requestUpdateList();
            $result = $this->processUpdateLists($result);
            $result = $this->processImportantUpdates($result);

            $this->vars['core'] = $result['core'] ?? false;
            $this->vars['hasUpdates'] = $result['hasUpdates'] ?? false;
            $this->vars['hasImportantUpdates'] = $result['hasImportantUpdates'] ?? false;
            $this->vars['pluginList'] = $result['plugins'] ?? [];
            $this->vars['themeList'] = $result['themes'] ?? [];
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return ['#updateContainer' => $this->makePartial('update_list')];
    }

    /**
     * onSyncProject synchronizes plugin packages with local packages
     */
    public function onSyncProject()
    {
        try {
            $installPackages = UpdateManager::instance()->syncProjectPackages();

            $updateSteps = [];

            foreach ($installPackages as $code => $installPackage) {
                $updateSteps[] = [
                    'code' => 'installPlugin',
                    'label' => __('Installing plugin: :name', ['name' => $code]),
                    'name' => $code
                ];
            }

            $updateSteps[] = [
                'code' => 'completeUpdate',
                'label' => __('Finishing update process'),
                'type' => 'final'
            ];

            $this->vars['updateSteps'] = $updateSteps;
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('execute');
    }

    /**
     * onApplyUpdates runs the composer update process
     */
    public function onApplyUpdates()
    {
        try {
            $updateSteps = [
                [
                    'code' => 'updateComposer',
                    'label' => __('Updating package manager')
                ],
                [
                    'code' => 'updateCore',
                    'label' => __('Updating application files')
                ],
                [
                    'code' => 'setBuild',
                    'label' => __('Setting build number')
                ],
                [
                    'code' => 'completeUpdate',
                    'label' => __('Finishing update process'),
                    'type' => 'final'
                ]
            ];

            $this->vars['updateSteps'] = $updateSteps;
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('execute');
    }

    /**
     * onInstallPlugin validates the plugin code and execute the plugin installation
     */
    public function onInstallPlugin()
    {
        try {
            if (!$code = trim(post('code'))) {
                throw new ApplicationException(__('Please specify a Plugin name to install.'));
            }

            $updateSteps = [
                [
                    'code' => 'installPlugin',
                    'label' => __('Installing plugin: :name', ['name' => $code]),
                    'name' => $code
                ],
                [
                    'code' => 'completeInstall',
                    'label' => __('Finishing installation process'),
                    'type' => 'final'
                ]
            ];

            $this->vars['updateSteps'] = $updateSteps;

            return $this->makePartial('execute');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
            return $this->makePartial('plugin_form');
        }
    }

    /**
     * onRemovePlugin removes an existing plugin
     */
    public function onRemovePlugin()
    {
        try {
            if (!$code = trim(post('code'))) {
                throw new ApplicationException(__('Please specify a Plugin name to install.'));
            }

            $updateSteps = [
                [
                    'code' => 'removePlugin',
                    'label' => __('Removing plugin: :name', ['name' => $code]),
                    'name' => $code
                ],
                [
                    'code' => 'completeUpdate',
                    'label' => __('Finishing installation process'),
                    'type' => 'final'
                ]
            ];

            $this->vars['updateSteps'] = $updateSteps;

            return $this->makePartial('execute');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
            return $this->makePartial('plugin_form');
        }
    }

    /**
     * onInstallTheme validates the theme code and execute the theme installation
     */
    public function onInstallTheme()
    {
        try {
            if (!$code = trim(post('code'))) {
                throw new ApplicationException(__('Please specify a Theme name to install.'));
            }

            $updateSteps = [
                [
                    'code' => 'installTheme',
                    'label' => __('Installing theme: :name', ['name' => $code]),
                    'name' => $code
                ],
                [
                    'code' => 'completeInstall',
                    'label' => __('Finishing installation process'),
                    'type' => 'final'
                ]
            ];

            $this->vars['updateSteps'] = $updateSteps;

            return $this->makePartial('execute');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
            return $this->makePartial('theme_form');
        }
    }

    /**
     * onRemoveTheme removes an existing theme
     */
    public function onRemoveTheme()
    {
        try {
            if (!$code = trim(post('code'))) {
                throw new ApplicationException(__('Please specify a Theme name to install.'));
            }

            $updateSteps = [
                [
                    'code' => 'removeTheme',
                    'label' => __('Removing theme: :name', ['name' => $code]),
                    'name' => $code
                ],
                [
                    'code' => 'completeUpdate',
                    'label' => __('Finishing installation process'),
                    'type' => 'final'
                ]
            ];

            $this->vars['updateSteps'] = $updateSteps;

            return $this->makePartial('execute');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
            return $this->makePartial('theme_form');
        }
    }

    /**
     * onExecuteStep runs a specific update step
     */
    public function onExecuteStep()
    {
        // Debugging
        $useDebug = System::checkDebugMode() && get('debug');

        // Prewarm system
        $this->prewarmSystem();

        $composer = ComposerManager::instance();
        $manager = UpdateManager::instance();
        $stepCode = post('code');

        $composer->setOutputBuffer();

        try {
            switch ($stepCode) {
                case 'updateComposer':
                    try {
                        $composer->update(['composer/composer']);
                        $this->clearMetaCache();
                    }
                    catch (Exception $ex) {
                    }
                    break;

                case 'updateCore':
                    $composer->update();
                    $this->clearMetaCache();
                    break;

                case 'installPlugin':
                    $manager->installPlugin(post('name'));
                    $this->clearMetaCache();
                    break;

                case 'installTheme':
                    $manager->installTheme(post('name'));
                    $this->clearMetaCache();
                    break;

                case 'removePlugin':
                    $manager->uninstallPlugin(post('name'));
                    $this->clearMetaCache();
                    break;

                case 'removeTheme':
                    $manager->uninstallTheme(post('name'));
                    $this->clearMetaCache();
                    break;

                case 'setBuild':
                    $manager->setBuildNumberManually();
                    break;

                case 'completeUpdate':
                    $manager->update();
                    Flash::success(__('Update process complete'));
                    return Redirect::refresh();

                case 'completeInstall':
                    $manager->update();
                    Flash::success(__('Plugin installed successfully'));
                    return Redirect::refresh();
            }
        }
        catch (Exception $ex) {
            if ($useDebug) {
                Log::error($ex);
            }

            throw new AjaxException([
                'error' => $ex->getMessage(),
                'output' => $composer->getOutputBuffer()
            ]);
        }

        if ($useDebug) {
            Log::info($composer->getOutputBuffer());
        }
    }

    /**
     * clearMetaCache will clear the system meta files that may cause a system
     * crash if the non-existent classes are referenced.
     */
    protected function clearMetaCache()
    {
        CacheHelper::instance()->clearMeta();
    }

    /**
     * processImportantUpdates loops the update list and checks for actionable updates
     */
    protected function processImportantUpdates(array $result): array
    {
        $hasImportantUpdates = false;

        // Core
        if (isset($result['core'])) {
            $coreImportant = false;

            foreach (array_get($result, 'core.updates', []) as $build => $description) {
                if (strpos($description, '!!!') === false) {
                    continue;
                }

                $detailsUrl = '//octobercms.com/support/articles/release-notes';
                $description = str_replace('!!!', '', $description);
                $result['core']['updates'][$build] = [$description, $detailsUrl];
                $coreImportant = $hasImportantUpdates = true;
            }

            $result['core']['isImportant'] = $coreImportant ? '1' : '0';
        }

        // Plugins
        foreach (array_get($result, 'plugins', []) as $code => $plugin) {
            $isImportant = false;

            foreach (array_get($plugin, 'updates', []) as $version => $description) {
                if (strpos($description, '!!!') === false) {
                    continue;
                }

                $isImportant = $hasImportantUpdates = true;
                $detailsUrl = Backend::url('system/market/plugin/'.PluginVersion::makeSlug($code).'/upgrades').'?fetch=1';
                $description = str_replace('!!!', '', $description);
                $result['plugins'][$code]['updates'][$version] = [$description, $detailsUrl];
            }

            $result['plugins'][$code]['isImportant'] = $isImportant ? '1' : '0';
        }

        $result['hasImportantUpdates'] = $hasImportantUpdates;

        return $result;
    }

    /**
     * processUpdateLists reverses the update lists for the core and all plugins.
     */
    protected function processUpdateLists(array $result): array
    {
        if ($core = array_get($result, 'core')) {
            $result['core']['updates'] = array_reverse(array_get($core, 'updates', []), true);
        }

        foreach (array_get($result, 'plugins', []) as $code => $plugin) {
            $result['plugins'][$code]['updates'] = array_reverse(array_get($plugin, 'updates', []), true);
        }

        return $result;
    }

    /**
     * prewarmSystem
     */
    protected function prewarmSystem()
    {
        // Address timeout limits
        @set_time_limit(3600);

        // Error path in case modules are replaced
        class_exists(\System\Classes\ErrorHandler::class);
        class_exists(\System\Models\EventLog::class);

        // Cache helper may be encoded by Source Guardian
        class_exists(\System\Helpers\Cache::class);
    }
}
