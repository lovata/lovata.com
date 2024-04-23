<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        // @deprecated remove if year >= 2024
        if (Schema::hasTable('backend_dashboards')) {
            Schema::table('backend_dashboards', function($table) {
                $table->renameColumn('created_by_user_id', 'created_user_id');
            });

            Schema::table('backend_dashboards', function($table) {
                $table->bigInteger('updated_user_id')->unsigned()->nullable();
            });
            return;
        }

        Schema::create('backend_dashboards', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index('dashboard_name_index');
            $table->string('slug')->unique('dashboard_slug_unique');
            $table->mediumText('definition')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('global_access')->default(false);
            $table->bigInteger('updated_user_id')->unsigned()->nullable();
            $table->bigInteger('created_user_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backend_dashboards');
    }
};
