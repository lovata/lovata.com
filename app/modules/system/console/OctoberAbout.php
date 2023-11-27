<?php namespace System\Console;

use Illuminate\Foundation\Console\AboutCommand as AboutCommandBase;
use System\Classes\UpdateManager;

/**
 * OctoberAbout command
 */
class OctoberAbout extends AboutCommandBase
{
    /**
     * @var string signature for the console command.
     */
    protected $signature = 'october:about {--only= : The section to display}
        {--json : Output the information as JSON}';

    /**
     * @var string description for the console command.
     */
    protected $description = 'Display basic information about this application';

    /**
     * Gather information about the application.
     *
     * @return void
     */
    protected function gatherApplicationInformation()
    {
        static::addToSection('October CMS', fn () => [
            'October CMS Version' => UpdateManager::instance()->getCurrentVersion(),
            'Plugin Updates Count' => UpdateManager::instance()->check(),
        ]);

        parent::gatherApplicationInformation();
    }
}
