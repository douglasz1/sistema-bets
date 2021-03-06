<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Services\Admin\BetsService;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    /**
     * @var BetsService
     */
    private $service;

    public function __construct(BetsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('admin.cashier.index');
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

            $bets = $this->service
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
            $betId = $request->has('betId')
                ? $request->get('betId') : null;

            $bets = $this->service
                ->setBetId($betId)
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
