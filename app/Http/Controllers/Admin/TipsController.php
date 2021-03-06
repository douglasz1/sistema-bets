<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Services\BetsService;
use Bets\Services\TipsService;
use Illuminate\Http\Request;

class TipsController extends Controller
{
    /**
     * @var TipsService
     */
    private $tipsService;

    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(BetsService $betsService, TipsService $tipsService)
    {
        $this->betsService = $betsService;
        $this->tipsService = $tipsService;
    }

    public function cancel($id)
    {
        try {
            $this->tipsService->cancel($id);

            return response()->json([
                'message' => 'Palpite cancelado com sucesso!',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao cancelar palpite',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tip = $this->tipsService->update($request->all(), $id);

            $this->betsService->recalculateBet($tip->bet_id);

            return response()->json([
                'message' => 'Salvo com sucesso!',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao atualizar palpite',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
