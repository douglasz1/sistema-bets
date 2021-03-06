<?php

namespace Bets\Http\Controllers\API\CasaDasApostas;

use Bets\Http\Requests\BetsStoreRequest;
use Bets\Models\Bet;
use Bets\Services\BetsService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class BilheteController extends Controller
{
    /**
     * @var Bet
     */
    private $bet;
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(Bet $bet, BetsService $betsService)
    {
        $this->bet = $bet;
        $this->betsService = $betsService;
    }

    public function index($id)
    {
        try {
            $bet = $this->bet
                ->with([
                    'tips' => function ($query) {
                        return $query->select('bet_id', 'match_id', 'bet_slug', 'bet_name', 'choice_name', 'choice_slug', 'value', 'status');
                    },
                    'tips.match' => function ($query) {
                        return $query->select('match_id', 'league_id', 'sport_slug', 'home_team', 'away_team', 'match_date', 'status', 'home_1st', 'away_1st', 'home_2nd', 'away_2nd', 'home_final', 'away_final');
                    },
                    'tips.match.league'=> function ($query) {
                        return $query->select('name', 'league_id');
                    },
                    'seller' => function ($query) {
                        return $query->select('name', 'id');
                    }
                ])
                ->findOrFail($id);

            $camposParaOcultar = [
                'seller_id', 'company_id', 'cancel_id',
                'tips_quantity', 'commission', 'quotation_total',
                'updated_at', 'canceled_at', 'deleted_at',
            ];

            return response()->json($bet->makeHidden($camposParaOcultar)->toArray());

        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Simulação não encontrada!'
            ], 400);
        }
    }

    public function salvar(BetsStoreRequest $request)
    {
        try {
            $data = [
                'client_name' => $request->get('name'),
                'bet_value' => moneyUS($request->get('bet_value')),
                'quotations' => $request->get('choices'),
            ];

            $bet = $this->betsService->createWithoutSeller($data);

            return response()->json(['bet' => $bet]);

        } catch (\Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
