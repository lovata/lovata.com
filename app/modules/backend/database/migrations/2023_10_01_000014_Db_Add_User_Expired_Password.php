<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function (Blueprint $table) {
            $table->boolean('is_password_expired')->default(false);
            $table->timestamp('password_changed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('backend_users', function (Blueprint $table) {
            $table->dropColumn('is_password_expired');
            $table->dropColumn('password_changed_at');
        });
    }
};
