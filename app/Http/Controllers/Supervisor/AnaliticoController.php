<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Services\Supervisor\AnaliticoService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class AnaliticoController extends Controller
{
    /**
     * @var AnaliticoService
     */
    private $service;

    public function __construct(AnaliticoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('supervisor.reports.analitico');
    }

    public function report(Request $request)
    {
        try {
            $startDate = $request->has('startDate')
                ? $request->get('startDate') : null;

            $finalDate = $request->has('finalDate')
                ? $request->get('finalDate') : null;

            $status = $request->has('status')
                ? $request->get('status') : null;

            $companies = array_pluck(auth()->user()->companies->toArray(), 'id');

            $matches = $this->service
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->setCompanies($companies)
                ->setStatus($status)
                ->all();

            return response()->json([
                'matches' => $matches
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'result' => 'error',
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar partidas'
            ], 400);
        }
    }

    public function summary(Request $request, $id)
    {
        try {
            $match = $this->service->find($id);

            $companies = array_pluck(auth()->user()->companies->toArray(), 'id');

            $status = $request->has('status')
                ? $request->get('status') : null;

            $tips = $this->service
                ->setStatus($status)
                ->setCompanies($companies)
                ->findTips($match->match_id);

            return response()->json([
                'match' => $match,
                'tips' => $tips,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'result' => 'error',
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar partidas'
            ], 400);
        }
    }
}
