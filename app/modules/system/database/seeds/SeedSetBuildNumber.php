<?php namespace System\Database\Seeds;

use Seeder;
use System;
use System\Classes\UpdateManager;
use Exception;

/**
 * SeedSetBuildNumber
 */
class SeedSetBuildNumber extends Seeder
{
    /**
     * run
     */
    public function run($buildNumber = null)
    {
        $this->line('');

        try {
            if ($buildNumber) {
                UpdateManager::instance()->setBuild((int) $buildNumber);
            }
            else {
                $build = UpdateManager::instance()->setBuildNumberManually();
            }

            $this->line('* You are using October CMS version: v' . System::VERSION . '.' . $build, 'comment');
        }
        catch (Exception $ex) {
            $this->line('*** Unable to set build: [' . $ex->getMessage() . ']', 'comment');
        }
    }
}
