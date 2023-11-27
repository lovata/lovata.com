<?php namespace Lovata\Basecode\Console;

use DB;
use Illuminate\Console\Command;
use Schema;

/**
 * Class RemoveOldPluginsData
 * @package Lovata\Basecode\Console
 * @author  Igor Tverdokhleb, i.tverdokhleb@lovata.com, LOVATA Group
 */
class RemoveOldPluginsData extends Command
{
    protected $name = 'basecode:removeoldpluginsdata';

    protected $description = 'Remove old unsupported plugins DB records';

    public function handle()
    {
        $this->dropTables();
        $this->removeTrashRecords();
        $this->output->writeln('Done!');
    }

    private function dropTables()
    {
        $tablesToRemove = [
            'lovata_basecode_banner_set',
            'indikator_backend_trash',
        ];

        foreach ($tablesToRemove as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }

    private function removeTrashRecords()
    {
        $pluginNamespaceList = [
            'Bedard.AnalyticsExtension',
            'Flynsarmy.IdeHelper',
            'Indikator.Backend',
            'October.Demo',
            'October.Drivers',
            'RainLab.GoogleAnalytics',
            'ToughDeveloper.ImageResizer',
        ];

        foreach ($pluginNamespaceList as $pluginNamespace) {
            DB::table('system_plugin_history')->where('code', $pluginNamespace)->delete();
            DB::table('system_plugin_versions')->where('code', $pluginNamespace)->delete();
        }
    }
}
