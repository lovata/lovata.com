<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        // @deprecated remove if year >= 2024
        if (Schema::hasTable('cms_traffic_stats_pageviews')) {
            return;
        }

        Schema::create('cms_traffic_stats_pageviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('ev_datetime')->nullable()->index();
            $table->date('ev_date')->nullable()->index();
            $table->string('ev_year_month_day', 10)->nullable()->index();
            $table->string('ev_year_month', 10)->nullable()->index();
            $table->string('ev_year_quarter', 10)->nullable()->index();
            $table->string('ev_year_week', 10)->nullable()->index();
            $table->string('ev_year', 10)->nullable()->index();
            $table->timestamp('ev_timestamp')->useCurrent()->index();
            $table->boolean('user_authenticated')->nullable()->index();
            $table->string('client_id', 64)->nullable()->index();
            $table->boolean('first_time_visit')->default(false)->index();
            $table->string('user_agent')->nullable()->index();
            $table->string('page_path')->nullable()->index();
            $table->string('ip')->nullable();
            $table->string('city', 64)->nullable()->index();
            $table->string('country', 64)->nullable()->index();
            $table->string('referral_domain')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_traffic_stats_pageviews');
    }
};
