<?php


namespace Bets\Services\Simulador;


use Bets\Models\Match;

class PartidasService
{
    /**
     * @var Match
     */
    private $match;

    private $data = 'today';
    private $esporte;
    private $liga;

    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    public function buscarPartidas()
    {
        $query = $this->match->newQuery();

        $dataAtual = now();

        if ($this->data === 'amanha') {
            $query->whereDate('match_date', $dataAtual->addDay()->toDateString());

        } elseif ($this->data === 'depois_amanha') {
            $query->whereDate('match_date', $dataAtual->addDays(2)->toDateString());
        } else {
            $query->whereDate('match_date', $dataAtual->toDateString());
            $query->where('match_date', '>', $dataAtual->toDateTimeString());
        }

        if (!is_null($this->liga)) {
            $query->where('league_id', $this->liga);
        }

        if (!is_null($this->esporte)) {
            $query->where('sport_id', $this->esporte);
        }

        $query->where('active', true);

        $query->whereTime('match_date', '>=', '06:00:00');

        $query->with(['league', 'league.country', 'league.sport', 'cotacoesPrincipais']);

        return $query->get();
    }

    public function cotacoes($eventoID)
    {
        $query = $this->match->newQuery();

        $query->with(['league', 'league.country', 'league.sport', 'quotations']);

        $query->where('match_id', $eventoID);

        return $query->firstOrFail();
    }

    /**
     * @param string $data
     * @return PartidasService
     */
    public function setData(string $data): PartidasService
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param mixed $esporte
     * @return PartidasService
     */
    public function setEsporte($esporte)
    {
        $this->esporte = $esporte;
        return $this;
    }

    /**
     * @param mixed $liga
     * @return PartidasService
     */
    public function setLiga($liga)
    {
        $this->liga = $liga;
        return $this;
    }
}
