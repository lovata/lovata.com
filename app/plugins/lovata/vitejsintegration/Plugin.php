<?php
declare(strict_types=1);

namespace Lovata\ViteJsintegration;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Innocenzi\Vite\Commands\ExportConfigurationCommand;
use Innocenzi\Vite\Configuration;
use Innocenzi\Vite\EntrypointsFinder\DefaultEntrypointsFinder;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;
use Innocenzi\Vite\HeartbeatCheckers\HeartbeatChecker;
use Innocenzi\Vite\HeartbeatCheckers\HttpHeartbeatChecker;
use Innocenzi\Vite\TagGenerators\CallbackTagGenerator;
use Innocenzi\Vite\TagGenerators\TagGenerator;
use Innocenzi\Vite\Vite;
use Lovata\ViteJsintegration\Classes\Console\NpmCi;
use System\Classes\PluginBase;
use Lovata\ViteJsintegration\Classes\ConfigResolver;
use Lovata\ViteJsintegration\Classes\Console\UpdateTsconfigCommand;
use Lovata\ViteJsintegration\Classes\Console\ViteBuild;
use Lovata\ViteJsintegration\Classes\Console\ViteWatch;
use Lovata\ViteJsintegration\Classes\Twig\Extension;

/**
 * Dump Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'ViteJS Integration',
            'description' => 'Integration vitejs',
            'author'      => 'Lovata',
            'icon'        => 'icon-code'
        ];
    }

    public function register()
    {
        parent::register();

        $this->registerDirectives();
        $this->registerCommands();
    }

    public function boot()
    {
        parent::boot();
        $this->registerDefaultConfiguration();
        $this->registerBindings();
    }

    private function registerBindings(): void
    {
        $this->app->singleton(Vite::class, fn () => new Vite());

        $this->app->bind(EntrypointsFinder::class, config('vite.interfaces.entrypoints_finder', DefaultEntrypointsFinder::class));
        $this->app->bind(HeartbeatChecker::class, config('vite.interfaces.heartbeat_checker', HttpHeartbeatChecker::class));
        $this->app->bind(TagGenerator::class, config('vite.interfaces.tag_generator', CallbackTagGenerator::class));
    }

    private function registerDirectives(): void
    {
        $this->app->afterResolving(Controller::class, function() {
            $controller = Controller::getController();
            $controller->getTwig()
                ->addExtension(new Extension($controller));
        });
    }

    private function registerDefaultConfiguration(): void
    {
//        if(\App::configurationIsCached()) {
//            return;
//        }

        $configResolver = new ConfigResolver(Theme::getActiveTheme());

        $defaultConfig = include __DIR__ .'/packages/vite.php';
        $defaultConfig['configs']['default'] = array_merge_recursive(
            $defaultConfig['configs']['default'],
            $configResolver->getThemeConfiguration()
        );

        $this->app->make('config')->set('vite', $defaultConfig);

        Vite::findManifestPathWith(static function(Configuration $configuration) {
           return sprintf('%s/%s',
               rtrim(Theme::getActiveTheme()->getPath(), '/'),
                'manifest.json'
           );
        });
    }

    private function registerCommands(): void
    {
        if (!\App::runningInConsole()) {
            return;
        }

        $this->registerConsoleCommand('vite.config', ExportConfigurationCommand::class);
        $this->registerConsoleCommand('vite.tsconfig', UpdateTsconfigCommand::class);
        $this->registerConsoleCommand('vite.build', ViteBuild::class);
        $this->registerConsoleCommand('vite.watch', ViteWatch::class);
        $this->registerConsoleCommand('vite.ci', NpmCi::class);
    }
}
