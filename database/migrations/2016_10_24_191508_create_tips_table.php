<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tips', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('bet_id')->unsigned();
            $table->foreign('bet_id')->references('id')->on('bets')->onDelete('cascade');

            $table->bigInteger('match_id')->unsigned();

            $table->string('bet_slug');
            $table->string('bet_name');
            $table->string('choice_name');
            $table->string('choice_slug');
            $table->decimal('value');

            $table->enum('status', array('pending', 'win', 'lose', 'canceled'))
                ->default('pending');

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
        Schema::dropIfExists('tips');
    }
}
