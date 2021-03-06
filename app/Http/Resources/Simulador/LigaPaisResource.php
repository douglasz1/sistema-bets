<?php

namespace Bets\Http\Resources\Simulador;

use Illuminate\Http\Resources\Json\Resource;

class LigaPaisResource extends Resource
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
        ];
    }
}
