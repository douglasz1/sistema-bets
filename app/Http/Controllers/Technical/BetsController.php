<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Services\BetsService;
use Illuminate\Http\Request;

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

        $tips = $bet->tips->sortBy('match.match_date')->values()->all();
        unset($bet['tips']);
        $bet['tips'] = $tips;

        return view('technical.bets.summary', compact('bet'));
    }

    public function cancel(Request $request, $id)
    {
        try {
            $bet = $this->betsService->cancel($id);

            if ($request->wantsJson() || $request->expectsJson()) {
                return compact('bet');
            }

            return redirect()->route('technical.cashier.index')->with('success', 'Bilhete cancelado com sucesso!');

        } catch (\Exception $e) {

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['message' => 'Erro ao cancelar! ' . $e->getMessage()], 400);
            }

            return redirect()->route('technical.cashier.index')->with('error', 'Erro ao cancelar! ' . $e->getMessage());
        }
    }
}
