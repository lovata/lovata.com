<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('deferred_bindings', 'sort_order')) {
            Schema::table('deferred_bindings', function (Blueprint $table) {
                $table->integer('sort_order')->nullable();
            });
        }
    }

    public function down()
    {
    }
};
