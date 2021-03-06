<?php

namespace Bets\Http\Controllers\API\CasaDasApostas;

use Bets\Models\Match;
use Bets\Services\CompaniesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class PartidasController extends Controller
{
    /**
     * @var Match
     */
    private $match;
    /**
     * @var CompaniesService
     */
    private $companiesService;

    public function __construct(Match $match, CompaniesService $companiesService)
    {
        $this->match = $match;
        $this->companiesService = $companiesService;
    }

    public function index()
    {
        $partidas = $this->match
            ->select(['id', 'match_id', 'league_id', 'sport_slug', 'home_team', 'away_team', 'match_date', 'quotations_qty'])
            ->whereHas('quotations', function ($query) {
                $query->where('bet_slug', 'full_time_result')
                    ->orWhere('bet_slug', 'to_win_match');
            })
            ->with([
                'quotations' => function ($query) {
                    $query->select(['id', 'match_id', 'bet_slug', 'bet_name', 'choice_name', 'choice_slug', 'value'])
                        ->where('bet_slug', 'full_time_result')
                        ->orWhere('bet_slug', 'to_win_match');
                }
            ])
            ->where('sport_slug', 'soccer')
            ->where('active', true)
            ->where('match_date', '>', Carbon::now()->toDateTimeString())
            ->orderBy('match_date')
            ->get();

        $quotationModifier = $this->companiesService->first()->quotation_modifier / 100;

        $partidas->each(function ($item) use ($quotationModifier) {
            $item->quotations->each(function ($item) use ($quotationModifier) {
                $item['value'] += ($item->value * $quotationModifier);
                if ((float)$item->value < 1.01) {
                    $item['value'] = 1.01;
                } elseif ((float)$item->value > 100) {
                    $item['value'] = 100;
                }
            }); // end of quotations->each block
        });

        return response()->json($partidas);
    }

    public function abrir($id)
    {
        try {
            $partida = $this->match
                ->select(['id', 'match_id', 'league_id', 'sport_slug', 'home_team', 'away_team', 'match_date', 'quotations_qty'])
                ->with([
                    'quotations' => function ($query) {
                        $query->select(['id', 'match_id', 'bet_slug', 'bet_name', 'choice_name', 'choice_slug', 'value']);
                    }
                ])
                ->where('match_id', $id)
                ->firstOrFail();

            $quotationModifier = $this->companiesService->first()->quotation_modifier / 100;
            $partida->quotations->each(function ($item) use ($quotationModifier) {
                $item['value'] += ($item->value * $quotationModifier);
                if ((float)$item->value < 1.01) {
                    $item['value'] = 1.01;
                } elseif ((float)$item->value > 100) {
                    $item['value'] = 100;
                }
            }); // end of quotations->each block

            if (in_array($partida->sport_slug, ['volleyball', 'basketball', 'handball'])) {
                $partida->quotations->push([
                    'choice_name' => "Empate",
                    'id' => 0,
                    'value' => 0,
                ]);

                $sorted = $partida->quotations->sortBy('choice_name');

                unset($partida->quotations);
                $partida->quotations = $sorted->values()->all();
            }

            return response()->json($partida);

        } catch (\Throwable $exception) {
            return response()->json([
                'resultado' => 'Erro ao abrir partida'
            ], 400);
        }
    }
}
