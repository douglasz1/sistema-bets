<?php


namespace Bets\Services\NovaApi;


use Bets\Jobs\NovaApi\SalvarEvento;
use Bets\Models\Sport;
use Bets\Services\CountriesService;
use Bets\Services\LeaguesService;
use Carbon\Carbon;

class EventosService
{
    /**
     * @var CountriesService
     */
    private $countriesService;
    /**
     * @var LeaguesService
     */
    private $leaguesService;

    public function __construct(CountriesService $countriesService, LeaguesService $leaguesService)
    {
        $this->countriesService = $countriesService;
        $this->leaguesService = $leaguesService;
    }

    public function criarEvento(array $dadosEvento, $esporte)
    {
        $esporte = Sport::query()->where('api_id', $esporte['api_id'])->first();

        $pais = $this->countriesService->firstOrCreate([
            'api_id' => $dadosEvento['country']['api_id'],
            'name' => $dadosEvento['country']['name'],
        ]);

        // Creates a league if it does not exist in the database
        $liga = $this->leaguesService->updateOrCreate([
            'league_id' => $dadosEvento['league_data']['api_id'],
            'name' => $dadosEvento['league_data']['name'],
            'sport_id' => $esporte->id,
            'country_id' => $pais->id,
            'flag' => "{$pais->api_id}.svg",
        ]);

        // Transforms the date and time of match to Brazilian time
        $dataPartida = Carbon::parse($dadosEvento['match_date']);

        if (Carbon::now()->isWeekend() && $dataPartida->isWeekday()) return false;

        if ($dataPartida->toDateString() > Carbon::now()->addDays(2)->toDateString()) return false;

        $evento = [
            'match_id' => $dadosEvento['api_id'],
            'league_id' => $liga['league_id'],
            'sport_id' => $esporte->id,
            'sport_slug' => $esporte->slug,
            'home_team' => $dadosEvento['home_team'],
            'away_team' => $dadosEvento['away_team'],
            'status' => $dadosEvento['status'],
            'match_date' => $dataPartida,
        ];

        if ($dadosEvento['status'] === 'canceled' || !$dadosEvento['active']) {
            $evento['active'] = false;
        }

        dispatch((new SalvarEvento($evento))->onQueue('matches'));

        return true;
    }
}
