<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolaoPartidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolao_partidas', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bolao_id');
            $table->foreign('bolao_id')
                ->references('id')
                ->on('boloes')->onDelete('cascade');

            $table->string('time_casa');
            $table->string('time_fora');
            $table->dateTime('data_partida');

            $table->string('placar_casa')->nullable();
            $table->string('placar_fora')->nullable();
            $table->enum('vencedor', ['1', 'X', '2'])->nullable();

            $table->enum('situacao', ['pendente', 'cancelada', 'terminada'])->default('pendente');

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
        Schema::dropIfExists('bolao_partidas');
    }
}
