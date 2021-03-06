<?php

namespace Bets\Http\Controllers\Supervisor;

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
        return view('supervisor.reports.acompanhamentoBolao');
    }

    public function relatorio(Request $request)
    {
        try {
            $dataInicial = $request->filled('dataInicial')
                ? $request->get('dataInicial') : null;

            $dataFinal = $request->filled('dataFinal')
                ? $request->get('dataFinal') : null;

            $empresaId = $request->filled('empresaId')
                ? $request->get('empresaId') : null;

            $gerenteId = $request->filled('gerenteId')
                ? $request->get('gerenteId') : null;

            $vendedorId = $request->filled('vendedorId')
                ? $request->get('vendedorId') : null;

            if (is_null($empresaId)) {
                $empresaId = array_pluck(auth()->user()->companies->toArray(), 'id');
            }

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

            $empresaId = array_pluck(auth()->user()->companies->toArray(), 'id');

            $apostas = $this->acompanhamentoService
                ->setEmpresa($empresaId)
                ->pesquisar($apostaId);

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
            $aposta = $this->apostasService->find($id);

            $empresaId = array_pluck(auth()->user()->companies->toArray(), 'id');

            if (!in_array($aposta->empresa_id, $empresaId)) {
                throw new \Exception('Erro ao cancelar! Você não pode acessar essa aposta');
            }

            $now = \Carbon\Carbon::now()->toDateTimeString();
            if ($aposta->bolao->data_limite < $now) {
                throw new \Exception('O bolão já iniciou, não pode mais cancelar essa aposta.');
            }

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
