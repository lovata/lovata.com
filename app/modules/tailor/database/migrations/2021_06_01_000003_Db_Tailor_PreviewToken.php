<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    /**
     * up migration
     */
    public function up()
    {
        Schema::create('tailor_preview_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->nullable()->index();
            $table->mediumText('route')->nullable();
            $table->string('token')->nullable()->index();
            $table->integer('count_use')->default(0);
            $table->integer('count_limit')->default(0);
            $table->integer('created_user_id')->unsigned()->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * down migration
     */
    public function down()
    {
        Schema::dropIfExists('tailor_preview_tokens');
    }
};
