<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('backend_user_preferences', function (Blueprint $table) {
            $table->integer('site_id')->nullable()->unsigned();
            $table->integer('site_root_id')->nullable()->unsigned();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('backend_user_preferences', 'site_id')) {
            Schema::dropColumns('backend_user_preferences', 'site_id');
        }

        if (Schema::hasColumn('backend_user_preferences', 'site_root_id')) {
            Schema::dropColumns('backend_user_preferences', 'site_root_id');
        }
    }
};
