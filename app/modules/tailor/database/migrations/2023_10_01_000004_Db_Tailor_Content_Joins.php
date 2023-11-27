<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tailor_content_joins', function (Blueprint $table) {
            $table->integer('parent_id')->nullable();
            $table->string('parent_type')->nullable();
            $table->integer('relation_id')->nullable();
            $table->string('relation_type')->nullable();
            $table->string('field_name')->nullable()->index();
            $table->integer('site_id')->nullable()->index();

            $table->index(['parent_id', 'parent_type'], $table->getTable().'_pidx');
            $table->index(['relation_id', 'relation_type'], $table->getTable().'_ridx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tailor_content_joins');
    }
};
