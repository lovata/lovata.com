<?php

use Tailor\Classes\Blueprint;
use Tailor\Classes\BlueprintIndexer;

class BlueprintIndexerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Blueprint::setDefaultDatasource(base_path('modules/tailor/tests/fixtures/blueprints'));
    }

    /**
     * testListingBlueprints
     */
    public function testListingBlueprints()
    {
        $this->markTestSkipped('Needs refactor to isolate blueprint paths');
        return;

        $sections = BlueprintIndexer::instance()->listSections();
        $this->assertCount(8, $sections);

        $mixins = BlueprintIndexer::instance()->listMixins();
        $this->assertCount(12, $mixins);

        $globals = BlueprintIndexer::instance()->listGlobals();
        $this->assertCount(1, $globals);
    }
}
