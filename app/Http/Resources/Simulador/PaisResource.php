<?php

namespace Bets\Http\Resources\Simulador;

use Illuminate\Http\Resources\Json\Resource;

class PaisResource extends Resource
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
            'nome' => $this['name'],
            'bandeira' => asset("storage/flags/{$this['api_id']}.svg"),
            'ligas' => $this->when(isset($this['campeonatos']), LigaPaisResource::collection($this['campeonatos']))
        ];
    }
}
