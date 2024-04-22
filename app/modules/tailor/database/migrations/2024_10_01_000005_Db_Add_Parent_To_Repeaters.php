<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('tailor_global_repeaters', 'parent_id')) {
            return;
        }

        Schema::table('tailor_global_repeaters', function (Blueprint $table) {
            $table->integer('parent_id')->nullable();
        });
    }

    public function down()
    {
    }
};
