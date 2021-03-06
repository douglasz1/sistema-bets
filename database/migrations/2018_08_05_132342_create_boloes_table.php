<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoloesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boloes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nome');
            $table->decimal('valor');
            $table->decimal('inicial');
            $table->decimal('acumulado');

            $table->dateTime('data_limite');
            $table->dateTime('data_finalizar');

            // premiações e porcentagens
            $table->tinyInteger('premio_1');
            $table->tinyInteger('premio_2');
            $table->tinyInteger('banca');
            $table->tinyInteger('vendedor');
            $table->tinyInteger('bonus_vendedor');
            $table->tinyInteger('sistema');

            $table->boolean('imprimir_apurado');
            $table->enum('tipo_bolao', ['simples', 'completo'])->default('simples');
            $table->enum('situacao', ['pendente', 'finalizado'])->default('pendente');

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
        Schema::dropIfExists('boloes');
    }
}
