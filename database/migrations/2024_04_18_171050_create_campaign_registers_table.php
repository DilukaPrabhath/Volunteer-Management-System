<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_registers', function (Blueprint $table) {
            $table->id();
            $table->integer('registered_user_id');
            $table->integer('campaign_id');
            $table->text('description')->nullable();
            $table->time('week_days_start_time')->nullable();
            $table->time('week_days_end_time')->nullable();
            $table->time('week_end_days_start_time')->nullable();
            $table->time('week_end_days_end_time')->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_registers');
    }
}
