<?php

namespace Bets\Services;

use Bets\Jobs\SaveMatchAndQuotations;
use Bets\Models\Match;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MatchesService
{
    /**
     * @var Match
     */
    private $match;
    /**
     * @var ResultsService
     */
    private $resultsService;
    /**
     * @var LeaguesService
     */
    private $leaguesService;

    public function __construct(Match $match, ResultsService $resultsService, LeaguesService $leaguesService)
    {
        $this->match = $match;
        $this->resultsService = $resultsService;
        $this->leaguesService = $leaguesService;
    }

    /**
     * @param array $data
     * @return \Bets\Models\Match
     */
    public function create(array $data)
    {
        $match = $this->match->create($data);

        $this->resultsService->create($data);

        return $match;
    }

    public function findById($id)
    {
        return $this->match->find($id);
    }

    public function matchesByLeague($leagueId)
    {
        return $this->match
            ->where('league_id', $leagueId)
            ->where('match_date', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->with(['quotations' => function ($query) {
                $query->where('bet_name', 'Match Result')->orderBy('choice_name', 'ASC');
                $query->orWhere('bet_name', 'Resultado')->orderBy('choice_name', 'ASC');
            }])->get();
    }

    public function allMatchesWithQuotations()
    {
        return $this->match
            ->where('match_date', '>', Carbon::now()->format('Y-m-d H:i:s'))
            ->with(['quotations' => function ($query) {
                $query->where('bet_name', 'Match Result')->orderBy('choice_name', 'ASC');
                $query->orWhere('bet_name', 'Resultado')->orderBy('choice_name', 'ASC');
            }])
            ->get();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->match->where('match_date', '>', Carbon::now()->format('Y-m-d H:i:s'))->get();
    }

    public function cleanMatches()
    {
        $dateToDelete = Carbon::now()->subDays(7)->toDateString();
        return $this->match->whereDate('match_date', '<', $dateToDelete)->delete();
    }

    public function delete($id)
    {
        return $this->match->destroy($id);
    }

    public function update(array $data, $id)
    {
        $this->resultsService->update($data, $id);
        return $this->match->where('id', $id)->update($data);
    }

    public function alternateStatus($id, $tipo = 'pre')
    {
        $match = $this->match->newQuery()->find($id);

        $banca = \Bets\Models\Banca::query()->first();

        if ($tipo === 'pre') {
            $partidas = $banca->partidas_inativas_pre;
            $index = array_search($match->match_id, $partidas);

            if ($index !== false) {
                unset($partidas[$index]);
            } else {
                array_push($partidas, $match->match_id);
            }

            $banca->partidas_inativas_pre = $partidas;
        } else {
            $partidas = $banca->partidas_inativas_live;
            $index = array_search($match->match_id, $partidas);

            if ($index !== false) {
                unset($partidas[$index]);
            } else {
                array_push($partidas, $match->match_id);
            }

            $banca->partidas_inativas_live = $partidas;
        }

        $match->active = !$match->active;

        $match->save();
        $banca->save();

        cache()->forget('todayMatches');

        return $match;
    }

    public function updateOrCreate(array $attributes)
    {
        try {
            $match = $this->match
                ->where('match_id', $attributes['match_id'])
                ->firstOrFail();

            if ($match->status === 'canceled' || $match->status === 'finished') {
                unset($attributes['status']);
            }

            if (!$match->active) {
                unset($attributes['active']);
            }

            $match->fill($attributes);
            $match->save();

            $this->resultsService->updateOrCreate($attributes);

            return $match;

        } catch (ModelNotFoundException $exception) {
            return $this->create($attributes);
        }
    }

    public function createFromJson(array $data, $sport)
    {
        $country = app(CountriesService::class)->firstOrCreate([
            'api_id' => $data['country']['api_id'],
            'name' => $data['country']['name'],
        ]);

        // Creates a league if it does not exist in the database
        $league = $this->leaguesService->updateOrCreate([
            'league_id' => $data['league_data']['api_id'],
            'name' => $data['league_data']['name'],
            'sport_id' => $sport->id,
            'country_id' => !is_null($country) ? $country->id : 1,
            'flag' => !is_null($country) ? "{$country->api_id}.svg" : 'flags/default.svg',
        ]);

        if (!$league->active) return false;

        // Transforms the date and time of match to Brazilian time
        $matchDate = Carbon::parse($data['match_date']);

        if (Carbon::now()->isWeekend() && $matchDate->isWeekday()) return false;

        if ($matchDate->toDateString() > Carbon::now()->addDays(2)->toDateString()) return false;

        $match = [
            'match_id' => $data['api_id'],
            'league_id' => $league['league_id'],
            'sport_id' => $sport->id,
            'sport_slug' => $sport->slug,
            'home_team' => $data['home_team'],
            'away_team' => $data['away_team'],
            'status' => $data['status'],
            'match_date' => $matchDate,
        ];

        if ($data['status'] === 'canceled' || !$data['active']) {
            $match['active'] = false;
        }

        dispatch((new SaveMatchAndQuotations($match))->onQueue('matches'));

        return true;
    }

    public function getByFilters(array $filters)
    {
        return $this->match
            ->when(isset($filters['active']), function ($query) use ($filters) {
                return $query->where('active', (boolean)$filters['active']);
            })
            ->when(empty($filters['start_date']), function ($query) use ($filters) {
                return $query->where('match_date', '>=', now()->toDateTimeString());
            })
            ->when(!empty($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('match_date', '>=', $filters['start_date']);
            })
            ->when(!empty($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('match_date', '<=', $filters['end_date']);
            })
            ->when(!empty($filters['league_id']), function ($query) use ($filters) {
                return $query->where('league_id', $filters['league_id']);
            })
            ->when(!empty($filters['sport_id']), function ($query) use ($filters) {
                return $query->where('sport_id', $filters['sport_id']);
            })
            ->with(['league' => function ($query) {
                return $query->select('name', 'id', 'league_id');
            }, 'sport'])
            ->orderBy('match_date', 'ASC')
            ->get();
    }

    public function getWithQuotations(array $filters)
    {
        return $this->match
            ->when(empty($filters['start_date']), function ($query) use ($filters) {
                return $query->where('match_date', '>=', now()->toDateTimeString())
                    ->whereDate('match_date', now()->toDateString());
            })
            ->when(!empty($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('match_date', '>=', $filters['start_date']);
            })
            ->when(!empty($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('match_date', '<=', $filters['end_date']);
            })
            ->when(!empty($filters['league_id']), function ($query) use ($filters) {
                return $query->where('league_id', $filters['league_id']);
            })
            ->when(!empty($filters['sport_id']), function ($query) use ($filters) {
                return $query->where('sport_id', $filters['sport_id']);
            })
            ->whereHas('quotations', function ($query) {
                return $query->where('quotations.bet_slug', 'full_time_result')
                    ->orWhere('quotations.bet_slug', 'to_win_match');
            })
            ->with([
                'league' => function ($query) {
                    return $query->select('name', 'id', 'league_id', 'country_id');
                },
                'quotations' => function ($query) {
                    return $query
                        ->whereIn('bet_slug', [
                            'full_time_result',
                            'both_teams_to_score',
                            'double_chance',
                        ]);
                },
                'sport',
                'league.country'
            ])
            ->orderBy('match_date', 'ASC')
            ->get();
    }
}
