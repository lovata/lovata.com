<?php namespace Cms\Database\Seeds;

use Cms\Classes\CmsDemoTrafficDataGenerator;
use Seeder;

/**
 * SeedDemoTrafficData
 */
class SeedDemoTrafficData extends Seeder
{
    public function run()
    {
        CmsDemoTrafficDataGenerator::instance()->generate();
    }
}
