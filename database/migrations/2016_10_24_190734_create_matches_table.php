<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('match_id')->unsigned()->unique();

            $table->bigInteger('league_id')->unsigned()->index();
            $table->foreign('league_id')->references('league_id')->on('leagues')->onDelete('cascade');

            $table->string('home_team');
            $table->string('away_team');
            $table->dateTime('match_date');
            $table->smallInteger('quotations_qty')->default(0)->unsigned();

            $table->boolean('active')->default(true);

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
        Schema::dropIfExists('matches');
    }
}
