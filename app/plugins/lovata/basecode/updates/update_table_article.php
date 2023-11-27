<?php namespace Lovata\BaseCode\Updates;

use October\Rain\Database\Updates\Migration;
use October\Rain\Database\Schema\Blueprint;
use Schema;

/**
 * Class UpdateTableArticle
 * @package Lovata\BaseCode\Updates
 */
class UpdateTableArticle extends Migration
{
    const TABLE = 'lovata_good_news_articles';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $obTable) {
            $obTable->string('link')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $obTable) {
            $obTable->dropColumn(['link']);
        });
    }
}