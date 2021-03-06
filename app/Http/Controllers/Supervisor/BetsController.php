<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Services\BetsService;
use Bets\Services\UsersService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class BetsController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;
    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(BetsService $betsService, UsersService $usersService)
    {
        $this->betsService = $betsService;
        $this->usersService = $usersService;
    }

    public function summary($id)
    {
        try {
            $bet = $this->betsService->findById($id);

            $companiesIDs = array_pluck(auth()->user()->companies->toArray(), 'id');

            if (!$this->isMyUser($bet->company_id, $companiesIDs)) {
                throw new \Exception('Você não pode acessar essa aposta');
            }

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

    public function cancel(Request $request, $id)
    {
        try {
            $bet = $this->betsService->findById($id);

            $companiesIDs = auth()->user()->companies->toArray();

            if (!$this->isMyUser($bet->company_id, array_pluck($companiesIDs, 'id'))) {
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'message' => 'Erro ao cancelar! Você não pode acessar essa aposta'
                    ], 400);
                }

                return redirect()->route('supervisor.cashier.index')->with('error', 'Erro ao cancelar! Você não pode acessar essa aposta');
            }

            $now = \Carbon\Carbon::now()->toDateTimeString();
            foreach ($bet->tips as $tip) {
                if ($now > $tip->match->match_date) {
                    throw new \Exception("A partida {$tip->match->matchName()} já se iniciou, por isso a simulação não pode ser cancelada.");
                }
            }

            $this->betsService->cancel($id);

            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Bilhete cancelado com sucesso!',
                    'bet' => $bet
                ]);
            }

            return redirect()->route('supervisor.cashier.index')->with('success', 'Bilhete cancelado com sucesso!');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'message' => 'Erro ao cancelar aposta!',
                ], 400);
            }

            return redirect()->route('supervisor.cashier.index')->with('error', 'Erro ao cancelar! ' . $e->getMessage());
        }
    }

    private function isMyUser($userId, $companiesIDs)
    {
        return in_array($userId, $companiesIDs);
    }
}
