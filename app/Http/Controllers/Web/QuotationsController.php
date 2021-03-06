<?php

namespace Bets\Http\Controllers\Web;

use Bets\Services\CompaniesService;
use Bets\Services\QuotationsService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class QuotationsController extends Controller
{
    /**
     * @var QuotationsService
     */
    private $quotationsService;
    /**
     * @var CompaniesService
     */
    private $companiesService;

    public function __construct(QuotationsService $quotationsService, CompaniesService $companiesService)
    {
        $this->quotationsService = $quotationsService;
        $this->companiesService = $companiesService;
    }

    public function quotations($matchId)
    {
        $quotationsArray = [];
        $quotationsByMatch = $this->quotationsService
            ->quotationByMatch($matchId, false);

        $quotationModifier = $this->companiesService->first()->quotation_modifier / 100;
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

                if (is_null($categoria)) {
                    $item['value'] = 0;
                }
            });

            $category = $betCategory->first();

            $quotations = $betCategory->toArray();

            $odds = collect($quotations);

            $filtrar = $odds->filter(function ($item, $key) {
                return $item['value'] > 0;
            });

            if ($filtrar->count() < 1) continue;

            $quotations = $filtrar->toArray();

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
