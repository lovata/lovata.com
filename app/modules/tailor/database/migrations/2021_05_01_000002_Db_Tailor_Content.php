<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tailor_content_schema', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name')->nullable()->index();
            $table->mediumText('meta')->nullable();
            $table->mediumText('fields')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop all tailor content before losing the schema
        Db::table('tailor_content_schema')->orderBy('id')->chunkById(100, function($tables) {
            foreach ($tables as $table) {
                $tablePrefix = substr($table->table_name, 0, -1);
                Schema::dropIfExists($tablePrefix.'c');
                Schema::dropIfExists($tablePrefix.'j');
                Schema::dropIfExists($tablePrefix.'r');
            }
        });

        Schema::dropIfExists('tailor_content_schema');
    }
};
