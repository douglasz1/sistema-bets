<?php

namespace Bets\Models\Bolao;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = 'bolao_partidas';

    protected $fillable = [
        'bolao_id', 'situacao',
        'time_casa', 'time_fora', 'data_partida',
        'placar_casa', 'placar_fora', 'vencedor',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'data_partida'
    ];


}
