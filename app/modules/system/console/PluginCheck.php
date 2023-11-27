<?php namespace System\Console;

use System\Helpers\Cache as CacheHelper;
use October\Rain\Composer\Manager as ComposerManager;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;

/**
 * PluginCheck checks for missing plugin dependencies and installs them
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginCheck extends Command
{
     /**
     * @var string name of console command
     */
    protected $name = 'plugin:check';

    /**
     * @var string description of the console command
     */
    protected $description = 'Checks for missing plugin dependencies and installs them.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->line('Checking Dependencies...');

        $this->installRequiredPlugins();
    }

    /**
     * installRequiredPlugins
     */
    protected function installRequiredPlugins()
    {
        $pluginRequire = \System\Classes\PluginManager::instance()->findMissingDependencies();
        $themeRequire = \Cms\Classes\ThemeManager::instance()->findMissingDependencies();

        $deps = array_unique(array_merge($pluginRequire, $themeRequire));

        // Prompt?
        // foreach ($deps as $dep) {
        //     $this->info('[ ] '.$dep);
        // }

        foreach ($deps as $dep) {
            $this->call('plugin:install', ['name' => $dep, '--no-migrate' => true, '--no-update' => true]);
        }

        if (count($deps)) {
            // Composer update
            $this->comment("Executing: composer update");

            $composer = ComposerManager::instance();
            $composer->setOutputCommand($this, $this->input);
            $composer->update();

            // Migrate database
            if (!$this->option('no-migrate')) {
                $this->comment("Executing: php artisan october:migrate");
                $this->line('');

                $errCode = null;
                static::passthruArtisan('october:migrate', $errCode);
                $this->line('');

                if ($errCode !== 0) {
                    $this->output->error('Migration failed. Check output above');
                    exit(1);
                }
            }
        }

        // Clear meta cache
        CacheHelper::instance()->clearMeta();

        // Success
        $this->info('All dependencies installed');
    }

    /**
     * getOptions get the console command options
     */
    protected function getOptions()
    {
        return [
            ['no-migrate', null, InputOption::VALUE_NONE, 'Do not run migration after install.'],
        ];
    }

    /**
     * passthruArtisan
     */
    protected static function passthruArtisan($command, &$errCode = null)
    {
        passthru('"'.PHP_BINARY.'" artisan ' .$command, $errCode);
    }
}
