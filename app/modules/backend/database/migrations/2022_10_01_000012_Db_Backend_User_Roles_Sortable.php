<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumns('backend_user_roles', ['sort_order', 'color_background'])) {
            Schema::table('backend_user_roles', function (Blueprint $table) {
                $table->integer('sort_order')->nullable();
                $table->string('color_background')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('backend_user_roles', 'sort_order')) {
            Schema::dropColumns('backend_user_roles', 'sort_order');
        }

        if (Schema::hasColumn('backend_user_roles', 'color_background')) {
            Schema::dropColumns('backend_user_roles', 'color_background');
        }
    }
};
