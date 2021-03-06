<?php

namespace Bets\Http\Controllers\Seller;

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
        return view('seller.acompanhamentoBolao');
    }

    public function relatorio(Request $request)
    {
        try {
            $dataInicial = $request->filled('dataInicial')
                ? $request->get('dataInicial') : null;

            $dataFinal = $request->filled('dataFinal')
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
