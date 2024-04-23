<?php

use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('backend_report_data_cache')) {
            return;
        }

        Schema::create('backend_report_data_cache', function($table) {
            $table->increments('id');
            $table->string('key')->index();
            $table->mediumText('value');
            $table->timestamps();
            $table->date('value_date');
            $table->index('created_at');
            $table->index(['key', 'value_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('backend_report_data_cache');
    }
};
