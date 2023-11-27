<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumns('system_site_definitions', ['is_role_restricted', 'allow_roles'])) {
            Schema::table('system_site_definitions', function (Blueprint $table) {
                $table->boolean('is_role_restricted')->default(0);
                $table->mediumText('allow_roles')->nullable();
            });
        }

        if (Schema::hasColumn('system_site_definitions', 'is_restricted')) {
            Schema::table('system_site_definitions', function (Blueprint $table) {
                $table->renameColumn('is_restricted', 'is_host_restricted');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('system_site_definitions', 'is_role_restricted')) {
            Schema::dropColumns('system_site_definitions', 'is_role_restricted');
        }

        if (Schema::hasColumn('system_site_definitions', 'allow_roles')) {
            Schema::dropColumns('system_site_definitions', 'allow_roles');
        }
    }
};
