<?php namespace Lovata\MightySeo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateSeoTemplate
 * @package Lovata\MightySeo\Updates
 */
class TableCreateSeoTemplate extends Migration
{
    const TABLE_NAME = 'lovata_mighty_seo_templates';

    /**
     * Apply migration
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->string('key');
            $obTable->text('value')->nullable();
            $obTable->integer('external_id')->nullable();
            $obTable->string('external_type')->nullable();
            $obTable->string('page_id')->nullable();
            $obTable->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}