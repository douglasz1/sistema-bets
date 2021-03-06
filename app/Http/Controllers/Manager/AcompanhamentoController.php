<?php

namespace Bets\Http\Controllers\Manager;

use Bets\Services\Bolao\AcompanhamentoService;
use Bets\Services\Bolao\ApostasService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class AcompanhamentoController extends Controller
{
    /**
     * @var ApostasService
     */
    private $apostasService;
    /**
     * @var AcompanhamentoService
     */
    private $acompanhamentoService;

    public function __construct(ApostasService $apostasService, AcompanhamentoService $acompanhamentoService)
    {
        $this->apostasService = $apostasService;
        $this->acompanhamentoService = $acompanhamentoService;
    }

    public function index()
    {
        return view('manager.relatorios.acompanhamentoGeralBolao');
    }

    public function geral(Request $request)
    {
        try {
            $dataInicial = $request->filled('dataInicial')
                ? $request->get('dataInicial') : null;

            $dataFinal = $request->filled('dataFinal')
                ? $request->get('dataFinal') : null;

            $gerenteId = auth()->id();

            $apostas = $this->acompanhamentoService
                ->setDataInicial($dataInicial)
                ->setDataFinal($dataFinal)
                ->setGerente($gerenteId)
                ->buscar(true);

            return response()->json([
                'apostas' => $apostas,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar apostas'
            ], 400);
        }
    }

    public function pessoal()
    {
        return view('manager.relatorios.acompanhamentoPessoalBolao');
    }

    public function relatorio(Request $request)
    {
        try {
            $dataInicial = $request->has('dataInicial')
                ? $request->get('dataInicial') : null;

            $dataFinal = $request->has('dataFinal')
                ? $request->get('dataFinal') : null;

            $vendedorId = auth()->id();

            $apostas = $this->acompanhamentoService
                ->setDataInicial($dataInicial)
                ->setDataFinal($dataFinal)
                ->setVendedor($vendedorId)
                ->buscar(false);

            return response()->json([
                'apostas' => $apostas
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar apostas'
            ], 400);
        }
    }
}
