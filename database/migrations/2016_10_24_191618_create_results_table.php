<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('match_id')->unsigned()->index();

            $table->bigInteger('league_id')->unsigned()->index();
            $table->foreign('league_id')->references('league_id')->on('leagues')->onDelete('cascade');

            $table->string('home_team');
            $table->string('away_team');
            $table->dateTime('match_date');

            $table->integer('home_1st')->nullable();
            $table->integer('away_1st')->nullable();

            $table->integer('home_2nd')->nullable();
            $table->integer('away_2nd')->nullable();

            $table->integer('home_final')->nullable();
            $table->integer('away_final')->nullable();

            $table->enum('status', array('pending', 'finished', 'canceled'))->default('pending');

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
        Schema::dropIfExists('results');
    }
}
