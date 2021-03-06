<?php

namespace Bets\Models;

use Illuminate\Database\Eloquent\Model;

class Banca extends Model
{
    protected $fillable = [
        'nome', 'codigo', 'site', 'regras',

        'ligas_inativas_pre', 'ligas_inativas_live',

        'partidas_inativas_pre', 'partidas_inativas_live',

        'cotacoes_inativas', 'ligas_principais',

        'bolao_disponivel', 'ao_vivo_disponivel',

        'comissoes_padrao', 'config_simulador',

        'tempo_cancelar', 'tempo_segunda_via',
    ];

    protected $casts = [
        'ligas_inativas_pre' => 'array',
        'ligas_inativas_live' => 'array',

        'partidas_inativas_pre' => 'array',
        'partidas_inativas_live' => 'array',

        'cotacoes_inativas' => 'array',
        'ligas_principais' => 'array',

        'comissoes_padrao' => 'array',
        'config_simulador' => 'array',

        'bolao_disponivel' => 'boolean',
        'ao_vivo_disponivel' => 'boolean',
    ];
}
