<?php

namespace Bets\Models\Esportes;

use Illuminate\Database\Eloquent\Model;

class CategoriaCotacao extends Model
{
    protected $table = 'esportes_categorias_cotacao';

    public $timestamps = false;

    protected $fillable = [
        'mercado', 'mercado_descricao',
        'palpite', 'palpite_descricao',
        'ativo', 'alterar_cotacao',
        'esporte',
    ];
}
