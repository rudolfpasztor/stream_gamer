<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('campaign_id');
            $table->integer('question_id');
            $table->integer('price');
            $table->integer('qty');
            $table->text('winners')->nullable();
            $table->string('status')->default('passive');
            $table->string('image_url')->nullable()->default('');
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('question_id')->references('id')->on('questions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drops');
    }
}
