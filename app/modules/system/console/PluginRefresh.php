<?php namespace System\Console;

use App;
use Illuminate\Console\Command;
use System\Classes\UpdateManager;
use System\Classes\PluginManager;
use October\Rain\Database\Updater;

/**
 * PluginRefresh refreshes a plugin or the app directory.
 *
 * This destroys all database tables for a specific plugin, then builds them up again.
 * It is a great way for developers to debug and develop new plugins.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class PluginRefresh extends Command
{
    use \Illuminate\Console\ConfirmableTrait;

    /**
     * @var string signature for the command
     */
    protected $signature = 'plugin:refresh
        {namespace : App or Plugin Namespace. <info>(eg: Acme.Blog)</info>}
        {--f|force : Force the operation to run.}
        {--rollback=false : Specify a version to rollback to, otherwise rollback to the beginning.}
        {--skip-errors : Continue with migration through exceptions.}';

    /**
     * @var string description of the console command
     */
    protected $description = 'Rollback and migrate database tables for a plugin.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $skipErrors = $this->option('skip-errors');
        if ($skipErrors) {
            Updater::skipErrors();
        }

        if ($this->isAppNamespace()) {
            $this->handleApp();
        }
        else {
            $this->handlePlugin();
        }

        if ($skipErrors) {
            Updater::skipErrors(false);
        }
    }

    /**
     * handleApp refreshes the app namespace
     *
     * @todo this method should be properly isolated to the "App" namespace, it currently
     * relies on a file not being found to protect tables and if the internals are ever
     * made smarter to locate missing migration files, it could be seriously problematic.
     */
    public function handleApp()
    {
        $message = "This will DESTROY database tables for the app directory.";
        if (!$this->confirmToProceed($message)) {
            return;
        }

        // This rollback depends on vendor logic, which may be unsafe (see below)
        $message = "Do not run this command without a backup of the database.";
        if (!$this->confirmToProceed($message)) {
            return;
        }

        $this->components->info('Rolling back app migrations.');

        $manager = UpdateManager::instance()->setNotesCommand($this);
        $manager->rollbackApp();

        if (!$this->isRollback()) {
            $manager->migrateApp();
            $manager->seedApp();
        }
    }

    /**
     * handlePlugin refreshes a plugin
     */
    public function handlePlugin()
    {
        $manager = PluginManager::instance();
        $name = $manager->normalizeIdentifier($this->argument('namespace'));

        if (!$manager->hasPlugin($name)) {
            return $this->output->error("Unable to find plugin [{$name}]");
        }

        $message = "This will DESTROY database tables for plugin [{$name}].";
        if ($this->isRollback()) {
            $toVersion = $this->option('rollback');
            $message = "This will DESTROY database tables for plugin [{$name}] up to version [{$toVersion}].";
        }

        if (!$this->confirmToProceed($message)) {
            return;
        }

        if ($this->isRollback()) {
            return $this->handleRollback($name);
        }
        else {
            return $this->handleRefresh($name);
        }
    }

    /**
     * handleRefresh performs a database rollback
     */
    protected function handleRefresh($name)
    {
        // Rollback plugin migration
        $manager = UpdateManager::instance()->setNotesCommand($this);
        $manager->rollbackPlugin($name);

        // Rerun migration
        $this->line('Reinstalling plugin...');
        $manager->migratePlugin($name);
    }

    /**
     * handleRollback performs a database rollback
     */
    protected function handleRollback($name)
    {
        // Rollback plugin migration
        $manager = UpdateManager::instance()->setNotesCommand($this);

        if ($toVersion = $this->option('rollback')) {
            $manager->rollbackPluginToVersion($name, $toVersion);
        }
        else {
            $manager->rollbackPlugin($name);
        }
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

    /**
     * isRollback overcomes an issue where Laravel no longer provides an optional option
     */
    protected function isRollback(): bool
    {
        return $this->option('rollback') !== 'false';
    }

    /**
     * isAppNamespace
     */
    protected function isAppNamespace(): bool
    {
        return mb_strtolower(trim($this->argument('namespace'))) === 'app';
    }
}
