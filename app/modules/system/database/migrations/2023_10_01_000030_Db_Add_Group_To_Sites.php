<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('system_site_definitions', 'group_id')) {
            Schema::table('system_site_definitions', function (Blueprint $table) {
                $table->integer('group_id')->index()->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('system_site_definitions', 'group_id')) {
            Schema::dropColumns('system_site_definitions', 'group_id');
        }
    }
};
