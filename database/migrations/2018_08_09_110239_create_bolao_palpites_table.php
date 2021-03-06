<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolaoPalpitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolao_palpites', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bolao_id');
            $table->foreign('bolao_id')->references('id')->on('boloes')->onDelete('cascade');

            $table->unsignedInteger('aposta_id');
            $table->foreign('aposta_id')->references('id')->on('bolao_apostas')->onDelete('cascade');

            $table->unsignedTinyInteger('palpite_casa');
            $table->unsignedTinyInteger('palpite_fora');

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
        Schema::dropIfExists('bolao_palpites', function (Blueprint $table) {
            //
        });
    }
}
