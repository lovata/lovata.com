<?php namespace Lovata\Basecode\Console;

use Illuminate\Console\Command;
use DB;
use Schema;
use System\Classes\PluginManager;

/**
 * MigrateMagicForms Command
 *
 * @link https://docs.octobercms.com/3.x/extend/console-commands.html
 */
class MigrateMagicForms extends Command
{
    const MARTIN_FORMS_PLUGIN = 'Martin.Forms';

    protected $name = 'basecode:migratemagicforms';

    protected $description = 'Migrate MagicForms plugin data to BlakeJones version';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        if (!PluginManager::instance()->hasPlugin(self::MARTIN_FORMS_PLUGIN)) {
            $this->output->writeln('The plugin has already been migrated!');

            return;
        }

        $this->removeBlakejonesTables();
        $this->renameMartinFormsTableToBlakejones();
        $this->removeMartinFormsMigrationHistory();
        $this->output->writeln('Done!');
    }

    private function removeBlakejonesTables()
    {
        Schema::dropIfExists('blakejones_magicforms_records');
    }

    private function renameMartinFormsTableToBlakejones()
    {
        Schema::rename('martin_forms_records', 'blakejones_magicforms_records');
    }

    private function removeMartinFormsMigrationHistory()
    {
        DB::table('system_plugin_history')->where('code', self::MARTIN_FORMS_PLUGIN)->delete();
        DB::table('system_plugin_versions')->where('code', self::MARTIN_FORMS_PLUGIN)->delete();
    }
}
