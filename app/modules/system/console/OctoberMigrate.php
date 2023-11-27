<?php namespace System\Console;

use Illuminate\Console\Command;
use System\Classes\UpdateManager;
use October\Rain\Database\Updater;

/**
 * OctoberMigrate migrates the database up or down.
 *
 * This builds up all database tables that are registered for October CMS and all plugins.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class OctoberMigrate extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string signature for the console command.
     */
    protected $signature = 'october:migrate {--f|force : Force the operation to run.}
        {--r|rollback : Destroys all database tables and records.}
        {--skip-errors : Continue with migration through exceptions.}';

    /**
     * @var string description of the console command
     */
    protected $description = 'Builds database tables for October CMS and all plugins.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $skipErrors = $this->option('skip-errors');
        if ($skipErrors) {
            Updater::skipErrors();
        }

        if ($this->option('rollback')) {
            return $this->handleRollback();
        }

        $this->line('Migrating Application and Plugins');

        UpdateManager::instance()->setNotesCommand($this)->update();

        if ($skipErrors) {
            Updater::skipErrors(false);
        }
    }

    /**
     * handleRollback performs a database rollback
     */
    protected function handleRollback()
    {
        if (!$this->confirmToProceed('This will DESTROY all database tables.')) {
            return;
        }

        UpdateManager::instance()->setNotesCommand($this)->uninstall();
    }

    /**
     * getDefaultConfirmCallback specifies the default confirmation callback
     */
    protected function getDefaultConfirmCallback()
    {
        return function() {
            return true;
        };
    }
}
