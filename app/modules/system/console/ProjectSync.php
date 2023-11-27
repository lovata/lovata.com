<?php namespace System\Console;

use System;
use System\Classes\UpdateManager;
use October\Rain\Composer\Manager as ComposerManager;
use Illuminate\Console\Command;
use Exception;

/**
 * ProjectSync installs all plugins and themes belonging to a project
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class ProjectSync extends Command
{
     /**
     * @var string name of console command
     */
    protected $name = 'project:sync';

    /**
     * @var string description of the console command
     */
    protected $description = 'Install plugins and themes belonging to a project.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->line('Synchronizing Project...');

        try {
            // Install project packages
            $this->installDefinedPlugins();

            // Composer update
            $this->comment("Executing: composer update");
            $composer = ComposerManager::instance();
            $composer->setOutputCommand($this, $this->input);
            $composer->update();

            // Check dependencies
            static::passthruArtisan('plugin:check --no-migrate');

            // Lock themes
            if (System::hasModule('Cms')) {
                static::passthruArtisan('theme:check');
            }

            // Migrate database
            $this->comment("Executing: php artisan october:migrate");
            $this->line('');

            $errCode = null;
            static::passthruArtisan('october:migrate', $errCode);

            if ($errCode !== 0) {
                $this->output->error('Migration failed. Check output above');
                exit(1);
            }

            $this->output->success("Project synchronized");
        }
        catch (Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * installDefinedPlugins
     */
    protected function installDefinedPlugins()
    {
        $installPackages = UpdateManager::instance()->syncProjectPackages();

        // Nothing to do
        if (count($installPackages) === 0) {
            $this->info('All packages already installed');
            return;
        }

        // Composer install differences
        foreach ($installPackages as $installPackage) {
            [$composerCode, $composerVersion] = $installPackage;
            $composerVersion = '^'.$composerVersion;
            $this->comment("Executing: composer require {$composerCode} {$composerVersion} --no-update");
            $this->line('');

            $composer = ComposerManager::instance();
            $composer->setOutputCommand($this, $this->input);
            $composer->addPackages([$composerCode => $composerVersion]);
        }
    }

    /**
     * passthruArtisan
     */
    protected static function passthruArtisan($command, &$errCode = null)
    {
        passthru('"'.PHP_BINARY.'" artisan ' .$command, $errCode);
    }
}
