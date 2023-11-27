<?php namespace Lovata\BaseCode\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableProjects
 * @package Lovata\BaseCode\Updates
 */
class CreateTableProjects extends Migration
{
    const TABLE = 'lovata_basecode_projects';

    /**
     * Up.
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE)) {
            return;
        }

        Schema::create(self::TABLE, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->boolean('active')->index()->default(0);
            $obTable->string('title');
            $obTable->string('preview_title')->nullable();
            $obTable->string('slug')->index();
            $obTable->integer('category_id')->index();
            $obTable->string('project_url')->nullable();
            $obTable->string('project_name')->nullable();
            $obTable->text('preview_text')->nullable();
            $obTable->text('preview_text_inner_page')->nullable();
            $obTable->text('content')->nullable();
            $obTable->text('tags')->nullable();
            $obTable->date('date')->index();
            $obTable->timestamps();
        });
    }

    /**
     * Down.
     */
    public function down()
    {
        Schema::drop(self::TABLE);
    }
}
