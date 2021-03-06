<?php

namespace Bets\Services\Simulador;


use Bets\Models\Sport;

class EsportesService
{
    /**
     * @var Sport
     */
    private $sport;

    public function __construct(Sport $sport)
    {
        $this->sport = $sport;
    }

    public function buscarEsportes()
    {
        $query = $this->sport->newQuery();

        $query->where('active', true);

        $query->whereHas('matches', function ($query) {
            return $query->whereDate('match_date', '>=', now()->toDateString());
        });

        return $query->get();
    }
}
