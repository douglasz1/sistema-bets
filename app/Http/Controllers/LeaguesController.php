<?php

namespace Bets\Http\Controllers;


use Bets\Services\CompaniesService;
use Bets\Services\LeaguesService;
use Bets\Services\UsersService;
use Cache;
use Illuminate\Http\Request;

class LeaguesController extends Controller
{
    /**
     * @var CompaniesService
     */
    private $companiesService;

    /**
     * @var LeaguesService
     */
    private $leaguesService;

    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(LeaguesService $leaguesService, UsersService $usersService, CompaniesService $companiesService)
    {
        $this->companiesService = $companiesService;
        $this->leaguesService = $leaguesService;
        $this->usersService = $usersService;
    }

    public function leaguesWithMatches(Request $request)
    {
        try {
            $filters = [];

            $filters['sport_id'] = $request->filled('sport_id') ?
                $request->get('sport_id') : 1;

            if ($request->filled('league_id')) {
                $filters['league_id'] = $request->get('league_id');
                unset($filters['sport_id']);
            }

            if ($request->filled('date')) {
                $filters['date'] = $request->get('date') !== 'all' ? $request->get('date') : null;
            }

            $nomeCache = 'eventos-' . implode('.', $filters);

            $leagues = Cache::tags('eventos-simulador')
                ->remember($nomeCache, now()->addMinute(), function () use ($filters) {
                    return $this->leaguesService->getLeaguesWithMatches($filters);
                });

            $user = auth()->user();
            $categoriasCotacao = categorias_cotacao('futebol');

            try {
                $quotationModifier = $user->quotationModifier() + ($user->company->quotation_modifier / 100);
            } catch (\Throwable $e) {
                $quotationModifier = $user->quotationModifier() + ($this->companiesService->first()->quotation_modifier / 100);
            }

            $cotacaoCasa = $categoriasCotacao->first(function ($categoria) {
                $mercado = $categoria['mercado'] === 'full_time_result';
                $palpite = $categoria['palpite'] === '1';
                return $mercado && $palpite;
            });

            $cotacaoEmpate = $categoriasCotacao->first(function ($categoria) {
                $mercado = $categoria['mercado'] === 'full_time_result';
                $palpite = $categoria['palpite'] === 'X';
                return $mercado && $palpite;
            });

            $cotacaoFora = $categoriasCotacao->first(function ($categoria) {
                $mercado = $categoria['mercado'] === 'full_time_result';
                $palpite = $categoria['palpite'] === '2';
                return $mercado && $palpite;
            });

            $dataAtual = now();

            $eventos = collect([]);
            $cotacao = 0;

            foreach ($leagues as $league) {

                $partidas = collect([]);

                foreach ($league->matches as $match) {

                    if ($match->match_date < $dataAtual) continue;

                    $match['human_date'] = $match->matchDate();
                    $match['match_name'] = $match->matchName();
                    $match['quotations_qty'] = $match->quotations_qty;
                    $match['quotations_url'] = route('matches.quotationsToJson', ['id' => $match->id], false);

                    foreach ($match->quotations as $quotation) {
                        $quotation['match_name'] = $match->match_name;
                        $quotation['match_date'] = $match->match_date->format('d/m H:i');

                        if ($quotation['choice_slug'] === '1') {
                            $cotacao = $cotacaoCasa['alterar_cotacao'] / 100;
                        } elseif ($quotation['choice_slug'] === 'X') {
                            $cotacao = $cotacaoEmpate['alterar_cotacao'] / 100;
                        } elseif ($quotation['choice_slug'] === '2') {
                            $cotacao = $cotacaoFora['alterar_cotacao'] / 100;
                        }

                        $quotation['value'] = $quotation['value'] + ($quotation->value * ($quotationModifier + $cotacao));

                        $quotation['value'] = number_format($quotation->value, 2);

                        if ($quotation->value < 1.01) {
                            $quotation['value'] = 1.01;
                        } elseif ($quotation->value > 100) {
                            $quotation['value'] = 100;
                        }
                    }

                    $partidas->push($match);
                }

                if ($partidas->isEmpty()) continue;

                $temp = $league->toArray();
                $temp['matches'] = $partidas->toArray();

                $eventos->push($temp);
            }

            return response()->json([
                'leagues' => $eventos,
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'leagues' => [],
            ], 404);
        }
    }

    public function leaguesHaveMatches()
    {
        $leagues = $this->leaguesService->getLeaguesThatHaveMatches();

        return compact('leagues');
    }

    public function jsonToApp($id, $userId)
    {
        $filters = array(
            'league_id' => $id,
            'date' => 'today'
        );

        $league = $this->leaguesService->getLeaguesWithMatches($filters)->first();
        $quotationModifier = $this->usersService->findById($userId)->quotationModifier();

        $league->matches->each(function ($item) use ($quotationModifier) {
            $item['human_date'] = $item->match_date->format('d/m H:i');
            $item['match_name'] = $item->matchName();

            $item->quotations->each(function ($item) use ($quotationModifier) {
                $item['value'] += ($item->value * $quotationModifier);
                $item['value'] = number_format($item->value, 2);
                if ((float)$item->value < 1.01) {
                    $item['value'] = 1.01;
                } elseif ((float)$item->value > 100) {
                    $item['value'] = 100;
                }
            });
        });

        return compact('league');
    }

    public function printQuotations()
    {
        return view('printquotations');
    }

    public function printQuotationsPost()
    {
        $filters = array(
            'date' => 'today'
        );

        $leagues = $this->leaguesService->getLeaguesWithMatches($filters);
        $userId = auth()->id();

        return compact('leagues', 'userId');
    }

    public function search(Request $request)
    {
        if ($request->has('q')) {
            $leagues = $this->leaguesService->searchLeagues($request->get('q'));

            if ($leagues->count() === 0) {
                return response()->json(['message' => 'Nada encontrado'], 400);
            }

            $user = auth()->user();
            $quotationModifier = $user->quotationModifier() + ($user->company->quotation_modifier / 100);

            $categoriasCotacao = categorias_cotacao('futebol');

            $leagues->each(function ($item) use ($quotationModifier, $categoriasCotacao) {
                $item->matches->each(function ($item) use ($quotationModifier, $categoriasCotacao) {
                    $item['human_date'] = $item->matchDate();
                    $item['match_name'] = $item->matchName();
                    $item['quotations_qty'] = $item->quotations_qty;
                    $item['quotations_url'] = route('matches.quotationsToJson', ['id' => $item->id], false);

                    $match = $item;
                    $item->quotations->each(function ($item) use ($match, $quotationModifier, $categoriasCotacao) {
                        $item['match_name'] = $match->match_name;
                        $item['match_date'] = $match->match_date->format('d/m H:i');

                        $categoria = $categoriasCotacao->first(function ($categoria) use ($item) {
                            $mercado = $categoria['mercado'] === $item['bet_slug'];
                            $palpite = $categoria['palpite'] === $item['choice_slug'];
                            return $mercado && $palpite;
                        });

                        $item['value'] += ($item->value * ($quotationModifier + ($categoria->alterar_cotacao / 100)));
                        if ((float)$item->value < 1.01) {
                            $item['value'] = 1.01;
                        } elseif ((float)$item->value > 100) {
                            $item['value'] = 100;
                        }
                    });

                    $sport = $match->sport_slug;
                    if (in_array($sport, ['volleyball', 'basketball', 'handball'])) {
                        $match->quotations->push([
                            'choice_name' => "Empate",
                            'id' => 0,
                            'value' => 0,
                        ]);

                        $sorted = $match->quotations->sortBy('choice_name');

                        unset($match->quotations);
                        $match->quotations = $sorted->values()->all();
                    }
                });
            });

            return response()->json(['leagues' => $leagues]);
        }

        return response()->json(['message' => 'Nada encontrado'], 400);
    }
}
