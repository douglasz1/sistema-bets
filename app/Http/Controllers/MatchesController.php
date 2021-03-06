<?php

namespace Bets\Http\Controllers;

use Bets\Services\LeaguesService;
use Bets\Services\MatchesService;
use Bets\Services\QuotationsService;

class MatchesController extends Controller
{
    /**
     * @var QuotationsService
     */
    private $quotationsService;

    public function __construct(QuotationsService $quotationsService)
    {
        $this->quotationsService = $quotationsService;
    }

    public function quotations($matchId)
    {
        $quotations = $this->quotationsService->quotationByMatch($matchId, true);

        return view('quotations.index', compact('quotations'));
    }

    public function quotationsToJson($matchId)
    {
        $quotationsArray = [];
        $quotationsByMatch = $this->quotationsService
            ->quotationByMatch($matchId, false);

        $user = auth()->user();
        $quotationModifier = $user->quotationModifier() + ($user->company->quotation_modifier / 100);

        $categoriasCotacao = categorias_cotacao('futebol');

        foreach ($quotationsByMatch->groupBy('bet_slug') as $betCategory) {
            $betCategory->each(function ($item) use ($quotationModifier, $categoriasCotacao) {
                $item['match_name'] = $item->match->matchName();
                $item['match_date'] = $item->match->match_date->format('d/m H:i');

                $categoria = $categoriasCotacao->first(function ($categoria) use ($item) {
                    // if (in_array($item['bet_slug'], ['jogador-que-marca-qualquer-momento', 'jogador-que-marca-ultimo-gol', 'jogador-que-marca-1o-gol'])) {
                    //     return true;
                    // }

                    $mercado = $categoria['mercado'] === $item['bet_slug'];
                    $palpite = $categoria['palpite'] === $item['choice_slug'];
                    return $mercado && $palpite;
                });

                if (!is_null($categoria)) {
                    $item['value'] += ($item->value * ($quotationModifier + ($categoria['alterar_cotacao'] / 100)));
                    $item['value'] = number_format($item->value, 2);
                }

                if ((float)$item->value < 1.01) {
                    $item['value'] = 1.01;
                } elseif ((float)$item->value > 100) {
                    $item['value'] = 100;
                }
            });

            $category = $betCategory->first();

            $quotations = $betCategory->toArray();

            array_push(
                $quotationsArray,
                [
                    'name' => $category['bet_name'],
                    'slug' => $category['bet_slug'],
                    'quotations' => $quotations
                ]
            );
        }

        return response()->json([
            'result' => 'success',
            'quotations' => $quotationsArray
        ]);
    }
}
