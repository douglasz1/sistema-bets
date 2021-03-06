<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterarEsporteParaDesafio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Bets\Models\Sport::whereIn('id', [4, 5, 6])->update(['active' => false]);

        $esporte = \Bets\Models\Sport::find(7);

        $esporte->name = 'Desafio';
        $esporte->slug = 'desafio';
        $esporte->active = true;

        $esporte->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
