<?php namespace System\Database\Seeds;

use Seeder;
use System\Models\SiteDefinition;

/**
 * SeedSetupPrimarySite
 */
class SeedSetupPrimarySite extends Seeder
{
    public function run()
    {
        SiteDefinition::syncPrimarySite();
    }
}
