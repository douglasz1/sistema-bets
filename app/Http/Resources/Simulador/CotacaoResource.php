<?php

namespace Bets\Http\Resources\Simulador;

use Illuminate\Http\Resources\Json\Resource;

class CotacaoResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'partida_id' => $this['match_id'],
            'mercado' => $this['bet_slug'],
            'mercado_descricao' => $this['bet_name'],
            'palpite' => $this['choice_slug'],
            'palpite_descricao' => $this['choice_name'],
            'valor' => (float) $this['value'],
        ];
    }
}
