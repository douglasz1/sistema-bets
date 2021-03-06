<?php

namespace Bets\Models\Bolao;

use Bets\Models\User;
use Illuminate\Database\Eloquent\Model;

class Aposta extends Model
{
    protected $table = 'bolao_apostas';

    protected $fillable = [
        'bolao_id', 'situacao',
        'vendedor_id', 'cancelado_id',
        'cliente', 'total_pontos',
        'valor', 'comissao',
    ];

    protected $appends = ['data_aposta'];

    public function getDataApostaAttribute()
    {
        return $this->created_at->format('d/m H:i');
    }

    public function bolao()
    {
        return $this->belongsTo(Bolao::class, 'bolao_id');
    }

    public function palpites()
    {
        return $this->hasMany(Palpite::class, 'aposta_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
}
