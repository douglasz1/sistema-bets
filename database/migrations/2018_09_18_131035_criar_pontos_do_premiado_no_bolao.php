<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriarPontosDoPremiadoNoBolao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boloes', function (Blueprint $table) {
            $table->unsignedTinyInteger('pontuacao_1')
                ->default(0)->after('acumulado');

            $table->unsignedTinyInteger('pontuacao_2')
                ->default(0)->after('pontuacao_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boloes', function (Blueprint $table) {
            $table->dropColumn('pontuacao_1');
            $table->dropColumn('pontuacao_2');
        });
    }
}
