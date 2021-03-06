<?php

namespace Bets\Http\Resources\Simulador;

use Illuminate\Http\Resources\Json\Resource;

class PartidaResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->match_id,

            'nome' => "{$this->home_team} x {$this->away_team}",

            'data_partida' => date($this->match_date),

            'equipes' => [
                'casa' => $this->home_team,
                'fora' => $this->away_team,
            ],

            'liga' => [
                'id' => $this->league->league_id,
                'nome' => $this->league->name,
            ],

            'pais' => [
                'id' => $this->league->country->id,
                'nome' => $this->league->country->name
            ],

            'quantidade_cotacoes' => $this->when(isset($this->cotacoes), function () {
                return count($this->cotacoes);
            }),

            'cotacoes' => $this->when(isset($this->cotacoes), function () {
                return CotacaoResource::collection($this->cotacoes);
            }),

            'cotacoes_principais' => $this->when(isset($this->cotacoes_principais), function () {
                return CotacaoResource::collection($this->cotacoes_principais);
            }),
        ];
    }
}
