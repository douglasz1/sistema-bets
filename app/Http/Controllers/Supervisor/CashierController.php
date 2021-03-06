<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Services\Supervisor\BetsService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class CashierController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(BetsService $betsService)
    {
        $this->betsService = $betsService;
    }

    public function index()
    {
        return view('supervisor.cashier.index');
    }

    public function report(Request $request)
    {
        try {
            $startDate = $request->has('startDate')
                ? $request->get('startDate') : null;

            $finalDate = $request->has('finalDate')
                ? $request->get('finalDate') : null;

            $status = $request->has('status')
                ? $request->get('status') : 'all';

            $companyId = $request->has('companyId')
                ? $request->get('companyId') : null;

            $managerId = $request->has('managerId')
                ? $request->get('managerId') : null;

            $sellerId = $request->has('sellerId')
                ? $request->get('sellerId') : null;

            $supervisorCompanies = array_pluck(auth()->user()->companies->toArray(), 'id');

            if (is_null($companyId) || !in_array($companyId, $supervisorCompanies)) {
                $companyId = $supervisorCompanies;
            }

            $bets = $this->betsService
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->setStatus($status)
                ->setCompaniesIDs($companyId)
                ->setManager($managerId)
                ->setSellers($sellerId)
                ->getAll();

            return response()->json([
                'bets' => $bets
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar apostas'
            ], 400);
        }
    }

    public function search(Request $request)
    {
        try {
            $betId = $request->has('betId') ? $request->get('betId') : null;

            $companyId = array_pluck(auth()->user()->companies->toArray(), 'id');

            $bets = $this->betsService
                ->setBetId($betId)
                ->setCompaniesIDs($companyId)
                ->search();

            return response()->json([
                'bets' => $bets
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar apostas'
            ], 400);
        }
    }
}
