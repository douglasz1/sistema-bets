<?php

namespace Bets\Models\Bolao;

use Illuminate\Database\Eloquent\Model;

class Palpite extends Model
{
    protected $table = 'bolao_palpites';

    protected $fillable = [
        'bolao_id', 'aposta_id',
        'palpite_casa', 'palpite_fora',
    ];

    public function bolao()
    {
        return $this->belongsTo(Bolao::class, 'bolao_id');
    }

    public function aposta()
    {
        return $this->belongsTo(Aposta::class, 'aposta_id');
    }
}
