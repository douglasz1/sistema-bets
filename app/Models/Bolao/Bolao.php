<?php

namespace Bets\Models\Bolao;

use Illuminate\Database\Eloquent\Model;

class Bolao extends Model
{
    protected $table = 'boloes';

    protected $fillable = [
        'nome', 'valor', 'inicial', 'acumulado',
        'premio_1', 'premio_2', 'banca',
        'vendedor', 'bonus_vendedor', 'sistema',
        'imprimir_apurado', 'data_limite', 'data_finalizar',
        'tipo_bolao', 'situacao',
        'pontuacao_1', 'pontuacao_2',
    ];

    protected $casts = [
        'imprimir_apurado' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'data_limite',
        'data_finalizar',
    ];

    protected $appends = ['limite_apostas', 'limite_finalizar'];

    public function getLimiteApostasAttribute()
    {
        return $this->data_limite->format('d/m H:i');
    }

    public function getLimiteFinalizarAttribute()
    {
        return $this->data_finalizar->format('d/m H:i');
    }

    public function partidas()
    {
        return $this->hasMany(Partida::class, 'bolao_id');
    }

    public function apostas()
    {
        return $this->hasMany(Aposta::class, 'bolao_id');
    }
}
