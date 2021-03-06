<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Services\Bolao\AcompanhamentoService;
use Bets\Services\Bolao\ApostasService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class AcompanhamentoBolaoController extends Controller
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
        return view('technical.reports.acompanhamentoBolao');
    }

    public function relatorio(Request $request)
    {
        try {
            $dataInicial = $request->has('dataInicial')
                ? $request->get('dataInicial') : null;

            $dataFinal = $request->has('dataFinal')
                ? $request->get('dataFinal') : null;

            $empresaId = $request->has('empresaId')
                ? $request->get('empresaId') : null;

            $gerenteId = $request->has('gerenteId')
                ? $request->get('gerenteId') : null;

            $vendedorId = $request->has('vendedorId')
                ? $request->get('vendedorId') : null;

            $apostas = $this->acompanhamentoService
                ->setDataInicial($dataInicial)
                ->setDataFinal($dataFinal)
                ->setEmpresa($empresaId)
                ->setGerente($gerenteId)
                ->setVendedor($vendedorId)
                ->buscar();

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

    public function busca(Request $request)
    {
        try {
            $apostaId = $request->has('apostaId')
                ? $request->get('apostaId') : null;

            $apostas = $this->acompanhamentoService->pesquisar($apostaId);

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

    public function cancelar($id)
    {
        try {
            $aposta = $this->apostasService->cancelar($id);

            return response()->json([
                'aposta' => $aposta
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'result' => $exception->getMessage()
            ], 400);
        }
    }
}
