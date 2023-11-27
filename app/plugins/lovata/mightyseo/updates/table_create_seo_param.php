<?php namespace Lovata\MightySeo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateSeoParam
 * @package Lovata\MightySeo\Updates
 */
class TableCreateSeoParam extends Migration
{
    const TABLE_NAME = 'lovata_mighty_seo_params';

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
            $obTable->integer('external_id')->nullable();
            $obTable->string('external_type')->nullable();
            $obTable->string('page_id')->nullable();
            $obTable->text('seo_title')->nullable();
            $obTable->text('seo_description')->nullable();
            $obTable->text('seo_keywords')->nullable();
            $obTable->text('page_h1')->nullable();
            $obTable->text('page_description')->nullable();
            $obTable->string('robots_follow')->nullable();
            $obTable->string('robots_index')->nullable();
            $obTable->string('robots_index_addition')->nullable();
            $obTable->string('canonical_url')->nullable();
            $obTable->timestamps();

            $obTable->index('external_id');
            $obTable->index('external_type');
            $obTable->index('page_id');
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
