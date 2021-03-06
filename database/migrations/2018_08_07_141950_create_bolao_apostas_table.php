<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolaoApostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolao_apostas', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bolao_id');
            $table->foreign('bolao_id')->references('id')->on('boloes')->onDelete('cascade');

            $table->unsignedInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('cancelado_id')->nullable();
            $table->foreign('cancelado_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('cliente');
            $table->decimal('valor');
            $table->decimal('comissao');
            $table->unsignedSmallInteger('total_pontos');

            $table->enum('situacao', ['pendente', 'cancelado'])->default('pendente');

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
        Schema::dropIfExists('bolao_apostas');
    }
}
