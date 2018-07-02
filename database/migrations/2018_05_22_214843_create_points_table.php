<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('end_user_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->string('source');
            $table->integer('count');
            $table->timestamps();

            $table->foreign('end_user_id')->references('id')->on('users');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            //$table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('points');
    }
}
