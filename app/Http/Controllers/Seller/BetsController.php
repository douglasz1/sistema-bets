<?php

namespace Bets\Http\Controllers\Seller;

use Bets\Services\BetsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class BetsController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(BetsService $betsService)
    {
        $this->betsService = $betsService;
    }

    public function summary($id)
    {
        $bet = $this->betsService->findById($id);

        if ($bet->seller_id !== auth()->id()) {
            return redirect()->route('seller.cashier.index')->with('error', 'Você não pode acessar essa aposta');
        }

        return view('seller.bets.ticket', compact('bet'));
    }

    public function cancel($id)
    {
        try {
            $cancelTime = env('CANCEL_TIME', 0);

            if ($cancelTime <= 0) {
                throw new \Exception('Não é permitido cancelar apostas', 400);
            }

            $bet = $this->betsService->findById($id);

            if ($bet->seller_id !== auth()->id()) {
                throw new \Exception('Você não pode acessar essa aposta, pois ela pertence a outro cambista', 401);
            }

            $now = Carbon::now()->toDateTimeString();

            if ($bet->created_at->addMinutes($cancelTime) <= $now) {
                throw new \Exception('O tempo limite para cancelamento foi excedido');
            }

            foreach ($bet->tips as $tip) {
                if ($tip->match->match_date <= $now) {
                    $homeTeam = $tip->match->home_team;
                    $awayTeam = $tip->match->away_team;

                    throw new \Exception("A partida {$homeTeam} x {$awayTeam} já iniciou, não pode mais cancelar essa aposta.");
                }
            }

            $bet = $this->betsService->cancel($id);

            return response()->json([
                'bet' => $bet
            ]);

        } catch (\Throwable $exception) {
            $errorCode = $exception->getCode() ?: 400;
            return response()->json([
                'result' => $exception->getMessage()
            ], $errorCode);
        }
    }
}
