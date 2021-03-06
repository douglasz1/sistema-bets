<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Services\BetsService;
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
        try {
            $bet = $this->betsService->findById($id);

            $tips = $bet->tips->sortBy('match.match_date')->values()->all();
            unset($bet['tips']);
            $bet['tips'] = $tips;

            return response()->json([
                'bet' => $bet,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar aposta',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel($id)
    {
        try {
            $bet = $this->betsService->cancel($id);

            return response()->json([
                'bet' => $bet,
                'message' => 'Bilhete cancelado com sucesso!',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao cancelar aposta',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function remove(Request $request, $id)
    {
        try {
            $bet = $this->betsService->remove($id);

            if ($request->wantsJson() || $request->expectsJson()) {
                return compact('bet');
            }

            return redirect()->route('admin.cashier.monitoring')->with('success', 'Bilhete apagado com sucesso!');

        } catch (\Exception $e) {

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json(['message' => 'Erro ao apagar! ' . $e->getMessage()], 400);
            }

            return redirect()->route('admin.cashier.monitoring')->with('error', 'Erro ao apagar! ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->betsService->update($request->all(), $id);

            return response()->json([
                'message' => 'Salvo com sucesso!',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao atualizar aposta',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
