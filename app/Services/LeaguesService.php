<?php

namespace Bets\Services;


use Carbon\Carbon;
use Bets\Models\League;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class LeaguesService
{
    /**
     * @var League
     */
    private $league;

    /**
     * LeaguesService constructor.
     * @param League $league
     */
    public function __construct(League $league)
    {
        $this->league = $league;
    }

    public function pluck()
    {
        return $this->league->orderBy('name', 'ASC')->pluck('name', 'id');
    }

    public function pluckWithLeagueId()
    {
        return $this->league->orderBy('name', 'ASC')->pluck('name', 'league_id');
    }

    public function all()
    {
        return $this->league->newQuery()->with('country')->orderBy('name', 'ASC')->get();
    }

    public function getByFilters(array $filters, bool $thatHaveMatches = false)
    {
        return $this->league
            ->newQuery()
            ->with(['country' => function ($query) {
                $query->select('id', 'name');
            }])
            ->when($thatHaveMatches, function ($query) {
                return $query->whereHas('matches');
            })
            ->when(!empty($filters['select']), function ($query) use ($filters) {
                return $query->select($filters['select']);
            })
            ->when(key_exists('start_date', $filters), function ($query) use ($filters) {
                return $query->whereHas('matches', function ($query) use ($filters) {
                    return $query
                        ->whereDate('matches.match_date', '>=', $filters['start_date'])
                        ->whereDate('matches.match_date', '<=', $filters['end_date'])
                        ->whereHas('quotations', function ($query) {
                            return $query->where('quotations.bet_slug', 'full_time_result');
                        });
                });
            })
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * Get all leagues with match
     *
     * @return mixed
     */
    public function getLeaguesThatHaveMatches()
    {
        $banca = \Bets\Models\Banca::query()->first();

        $leagues = $this->league->newQuery()
            ->whereNotIn('league_id', $banca->ligas_inativas_pre)
            ->whereHas('matches', function ($query) use ($banca) {
                $query
                    ->where('matches.match_date', '>', now()->toDateTimeString())
                    ->whereTime('matches.match_date', '>=', '06:00:00')
                    ->whereNotIn('matches.match_id', $banca->partidas_inativas_pre)
                    ->where('matches.active', true)
                    ->whereHas('quotations', function ($query) {
                        return $query->where('quotations.bet_slug', 'full_time_result')
                            ->orWhere('quotations.bet_slug', 'to_win_match');
                    });
            })
            ->withCount([
                'matches' => function ($query) use ($banca) {
                    $query
                        ->where('matches.match_date', '>', now()->toDateTimeString())
                        ->whereTime('matches.match_date', '>=', '06:00:00')
                        ->whereNotIn('matches.match_id', $banca->partidas_inativas_pre)
                        ->where('matches.active', true)
                        ->whereHas('quotations', function ($query) {
                            return $query->where('quotations.bet_slug', 'full_time_result')
                                ->orWhere('quotations.bet_slug', 'to_win_match');
                        });
                }
            ])
            ->with(['sport', 'country'])
            ->orderByRaw(DB::raw('sign(`order`) desc'))
            ->orderBy('order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();

        return $leagues;
    }

    public function getLeaguesWithMatches(array $filters)
    {
        $betweenDates = now()->toDateTimeString();

        if (isset($filters['date']) && !empty($filters['date'])) {
            if ($filters['date'] === 'tomorrow') {
                $betweenDates = [
                    \Carbon\Carbon::tomorrow()->toDateTimeString(),
                    \Carbon\Carbon::tomorrow()->toDateString()
                ];
            } elseif ($filters['date'] === 'after') {
                $betweenDates = [
                    \Carbon\Carbon::tomorrow()->addDay()->toDateTimeString(),
                    \Carbon\Carbon::tomorrow()->addDay()->toDateString()
                ];
            } elseif ($filters['date'] === 'today') {
                $betweenDates = [
                    now()->toDateTimeString(),
                    now()->toDateString()
                ];
            }
        }

        $banca = \Bets\Models\Banca::query()->first();

        $preJogoInativas = $banca->ligas_inativas_pre;

        return $this->league->newQuery()
            ->whereNotIn('league_id', $preJogoInativas)
            ->when(!empty($filters['league_id']), function ($query) use ($filters) {
                return $query->where('league_id', $filters['league_id']);
            })
            ->when(!empty($filters['sport_id']), function ($query) use ($filters) {
                $query->where('sport_id', $filters['sport_id']);
            })
            ->whereHas('matches', function ($query) use ($betweenDates, $banca) {
                return $query
                    ->when(is_array($betweenDates), function ($query) use ($betweenDates) {
                        return $query
                            ->where('matches.match_date', '>', $betweenDates[0])
                            ->whereDate('matches.match_date', $betweenDates[1]);
                    })
                    ->when(!is_array($betweenDates), function ($query) use ($betweenDates) {
                        return $query->where('matches.match_date', '>', $betweenDates);
                    })
                    ->whereNotIn('matches.match_id', $banca->partidas_inativas_pre)
                    ->where('matches.active', true)
                    ->whereTime('matches.match_date', '>=', '06:00:00')
                    ->has('quotations');
            })
            ->with([
                'sport',
                'country',
                'matches' => function ($query) use ($betweenDates, $banca) {
                    return $query
                        ->when(is_array($betweenDates), function ($query) use ($betweenDates) {
                            return $query
                                ->where('matches.match_date', '>', $betweenDates[0])
                                ->whereDate('matches.match_date', $betweenDates[1]);
                        })
                        ->when(!is_array($betweenDates), function ($query) use ($betweenDates) {
                            return $query->where('matches.match_date', '>', $betweenDates);
                        })
                        ->orderBy('match_date')
                        ->whereNotIn('matches.match_id', $banca->partidas_inativas_pre)
                        ->where('matches.active', true)
                        ->whereTime('matches.match_date', '>=', '06:00:00')
                        ->has('quotations');
                },
                'matches.quotations' => function ($query) {
                    return $query->where('bet_slug', 'full_time_result')
                        ->orWhere('quotations.bet_slug', 'to_win_match');
                }
            ])
            ->orderByRaw(DB::raw('sign(`order`) desc'))
            ->orderBy('order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * @param $data
     * @return \Bets\Models\League
     */
    public function create(array $data)
    {
        $league = $this->league->create($data);

        return $league;
    }

    /**
     * @param $leagueId
     * @return \Bets\Models\League
     */
    public function findById($leagueId)
    {
        return $this->league->find($leagueId);
    }

    public function alternateStatus($ligaID, $tipo = 'pre')
    {
        $liga = League::query()->findOrFail($ligaID);

        $liga->active = !$liga->active;

        $banca = \Bets\Models\Banca::query()->first();

        if ($tipo === 'pre') {
            $ligas = (array)$banca->ligas_inativas_pre;
            $index = array_search($liga->league_id, $ligas);

            if ($index !== false) {
                unset($ligas[$index]);
            } else {
                array_push($ligas, $liga->league_id);
            }

            $banca->ligas_inativas_pre = $ligas;
        } else {
            $ligas = (array)$banca->ligas_inativas_live;
            $index = array_search($liga->league_id, $ligas);

            if ($index !== false) {
                unset($ligas[$index]);
            } else {
                array_push($ligas, $liga->league_id);
            }

            $banca->ligas_inativas_live = $ligas;
        }

        $liga->save();
        $banca->save();

        return $liga;
    }

    public function update(array $data, $id)
    {
        return $this->league->where('id', $id)->update($data);
    }

    /**
     * Creates a league if it does not exist in the database
     *
     * @param array $attributes
     * @return \Bets\Models\League
     */
    public function updateOrCreate(array $attributes)
    {
        try {
            $league = $this->league
                ->where('league_id', $attributes['league_id'])
                ->firstOrFail();

            $league->fill($attributes);
            $league->save();

            return $league;
        } catch (ModelNotFoundException $exception) {
            $league = $this->create($attributes);
            cache()->forget('activeLeagues');

            return $league;
        }
    }

    public function searchLeagues($search)
    {
        return $this->league
            ->where('active', true)
            ->whereHas('matches', function ($query) use ($search) {
                $now = Carbon::now()->toDateTimeString();
                return $query
                    ->where('active', true)
                    ->where('match_date', '>', $now)
                    ->where(function ($query) use ($search) {
                        return $query->where('home_team', 'like', "%{$search}%")
                            ->orWhere('away_team', 'like', "%{$search}%");
                    })
                    ->whereHas('quotations', function ($query) {
                        return $query->where('quotations.bet_slug', 'full_time_result')
                            ->orWhere('quotations.bet_slug', 'to_win_match');
                    });
            })
            ->with([
                'sport',
                'country',
                'matches' => function ($query) use ($search) {
                    $now = Carbon::now()->toDateTimeString();
                    return $query
                        ->where('active', true)
                        ->where('matches.match_date', '>', $now)
                        ->where(function ($query) use ($search) {
                            return $query->where('home_team', 'like', "%{$search}%")
                                ->orWhere('away_team', 'like', "%{$search}%");
                        })
                        ->whereHas('quotations', function ($query) {
                            return $query->where('quotations.bet_slug', 'full_time_result')
                                ->orWhere('quotations.bet_slug', 'to_win_match');
                        });
                },
                'matches.quotations' => function ($query) {
                    return $query->where('bet_slug', 'full_time_result')
                        ->orWhere('quotations.bet_slug', 'to_win_match');
                }
            ])
            ->orderByRaw(DB::raw('sign(`order`) desc'))
            ->orderBy('order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();
    }
}
