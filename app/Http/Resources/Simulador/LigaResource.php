<?php

namespace Bets\Http\Resources\Simulador;

use Illuminate\Http\Resources\Json\Resource;

class LigaResource extends Resource
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
            'id' => $this['league_id'],
            'nome' => $this['name'],
            'bandeira' => !is_null($this['flag']) ? asset("storage/flags/{$this['flag']}") : null,
            'pais' => [
                'id' => $this['country']['id'],
                'nome' => $this['country']['name']
            ],
            'esporte' => $this->when(isset($this['sport']), function () {
                return [
                    'id' => $this['sport']['id'],
                    'nome' => $this['sport']['name'],
                    'slug' => $this['sport']['slug'],
                ];
            }),
            'partidas' => $this['partidas']
        ];
    }
}
