<?php

namespace Bets\Http\Controllers\Web;

use Bets\Services\CompaniesService;
use Bets\Services\LeaguesService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;
use Cache;

class LeaguesController extends Controller
{
    /**
     * @var LeaguesService
     */
    private $leaguesService;
    /**
     * @var CompaniesService
     */
    private $companiesService;

    public function __construct(LeaguesService $leaguesService, CompaniesService $companiesService)
    {
        $this->leaguesService = $leaguesService;
        $this->companiesService = $companiesService;
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
                $filters['date'] = $request->get('date') !== 'all'
                    ? $request->get('date')
                    : null;
            }

            $nomeCache = 'eventos-' . implode('.', $filters);

            $leagues = Cache::tags('eventos-simulador')
                ->remember($nomeCache, now()->addMinute(), function () use ($filters) {
                    return $this->leaguesService->getLeaguesWithMatches($filters);
                });

            $quotationModifier = $this->companiesService->first()->quotation_modifier / 100;
            $categoriasCotacao = categorias_cotacao('futebol');

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
                    $match['quotations_url'] = route('web.quotations.match', ['id' => $match->id], false);

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
                'result' => 'error',
                'message' => $exception,
            ], 404);
        } // end of try catch block
    }

    public function leaguesHaveMatches()
    {
        $leagues = $this->leaguesService->getLeaguesThatHaveMatches();

        $sports = collect([]);

        foreach ($leagues->groupBy('sport_id') as $leagueCollection) {
            $sport = $leagueCollection->pluck('sport')->first();

            $sports->push([
                'id' => $sport->id,
                'name' => $sport->name,
                'slug' => $sport->slug,
                'leagues' => $leagueCollection,
            ]);
        }

        return response()->json([
            'result' => 'success',
            'sports' => $sports->sortByDesc('id')->toArray(),
        ]);
    }

    public function search(Request $request)
    {
        if ($request->has('q')) {
            $leagues = $this->leaguesService->searchLeagues($request->get('q'));

            if ($leagues->count() === 0) {
                return response()->json(['message' => 'Nada encontrado'], 400);
            }

            $quotationModifier = $this->companiesService->first()->quotation_modifier / 100;
            $categoriasCotacao = categorias_cotacao('futebol');

            $leagues->each(function ($item) use ($quotationModifier, $categoriasCotacao) {
                $item->matches->each(function ($item) use ($quotationModifier, $categoriasCotacao) {
                    $item['human_date'] = $item->matchDate();
                    $item['match_name'] = $item->matchName();
                    $item['quotations_qty'] = $item->quotations_qty;
                    $item['quotations_url'] = route('web.quotations.match', ['id' => $item->id], false);

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
