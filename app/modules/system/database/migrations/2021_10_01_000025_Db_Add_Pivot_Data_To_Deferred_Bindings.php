<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('deferred_bindings', 'pivot_data')) {
            Schema::table('deferred_bindings', function (Blueprint $table) {
                $table->mediumText('pivot_data')->nullable();
            });
        }
    }

    public function down()
    {
    }
};
