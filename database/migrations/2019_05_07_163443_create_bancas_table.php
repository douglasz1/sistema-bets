<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nome');
            $table->string('codigo');
            $table->string('site');

            $table->json('ligas_inativas_pre');
            $table->json('ligas_inativas_live');

            $table->json('partidas_inativas_pre');
            $table->json('partidas_inativas_live');

            $table->json('cotacoes_inativas');

            $table->json('ligas_principais');

            $table->json('comissoes_padrao');
            $table->json('config_simulador');

            $table->boolean('bolao_disponivel')->default(true);

            $table->boolean('ao_vivo_disponivel')->default(true);

            $table->string('tempo_cancelar')->default(15);
            $table->string('tempo_segunda_via')->default(15);

            $table->longText('regras')->nullable();

            $table->timestamps();
        });

        $ligasInativas = \Bets\Models\League::query()
            ->where('active', false)
            ->select('league_id')
            ->get()
            ->pluck('league_id')
            ->toArray();

        $ligasPrincipais = \Bets\Models\League::query()
            ->where('active', true)
            ->where('order', '>=', 1)
            ->select('league_id')
            ->get()
            ->pluck('league_id')
            ->toArray();

        $partidasInativas = \Bets\Models\Match::query()
            ->where('active', false)
            ->select('match_id')
            ->get()
            ->pluck('match_id')
            ->toArray();

        \Bets\Models\Banca::query()
            ->create([
                'nome' => config('app.name'),
                'tempo_cancelar' => env('CANCEL_TIME'),
                'tempo_segunda_via' => env('REPRINT_TIME'),
                'codigo' => \Illuminate\Support\Str::slug(config('app.name')),
                'site' => \Illuminate\Support\Str::slug(config('app.name')),
                'ligas_inativas_pre' => $ligasInativas,
                'ligas_inativas_live' => $ligasInativas,
                'ligas_principais' => $ligasPrincipais,
                'partidas_inativas_pre' => $partidasInativas,
                'partidas_inativas_live' => $partidasInativas,
                'cotacoes_inativas' => [],
                'config_simulador' => [
                    'alterar_cotacao' => 0,
                    'multiplicador' => 500,
                    'premio_maximo' => 12000,
                ],
                'comissoes_padrao' => [
                    'quotation_modifier' => 0,
                    'profit_percentage' => 0,
                    'manager_commission' => 0,
                    'balance' => 1000,
                    'daily_limit' => 100,
                    'sales_goal' => 0,
                    'limit' => 1000,
                    'max_prize' => 12000,
                    'max_prize_multiplier' => 500,
                    'one_tip_quotation_min' => 1.6,
                    'two_tip_quotation_min' => 1.10,
                    'three_tip_quotation_min' => 1.01,
                    'tips_min' => 1, 'tips_max' => 25,
                    'commission1' => 6, 'value_min1' => 2, 'value_max1' => 100,
                    'commission2' => 10, 'value_min2' => 2, 'value_max2' => 300,
                    'commission3' => 12, 'value_min3' => 2, 'value_max3' => 300,
                    'commission6' => 12, 'value_min6' => 2, 'value_max6' => 300,
                    'commission11' => 12, 'value_min11' => 2, 'value_max11' => 300,
                    'commission16' => 12, 'value_min16' => 2, 'value_max16' => 300,
                    'comissao_ao_vivo' => 8,
                ]
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancas');
    }
}
