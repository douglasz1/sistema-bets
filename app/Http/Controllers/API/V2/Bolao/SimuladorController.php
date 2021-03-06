<?php

namespace Bets\Http\Controllers\API\V2\Bolao;

use Bets\Services\Bolao\ApostasService;
use Bets\Services\Bolao\BolaoService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class SimuladorController extends Controller
{
    /**
     * @var BolaoService
     */
    private $bolaoService;
    /**
     * @var ApostasService
     */
    private $apostasService;

    public function __construct(BolaoService $bolaoService, ApostasService $apostasService)
    {
        $this->bolaoService = $bolaoService;
        $this->apostasService = $apostasService;
    }

    public function index()
    {
        $boloes = $this->bolaoService->abertos();
        $boloesEncerrados = $this->bolaoService->encerrados();

        return view('bolao.simulador', compact('boloes', 'boloesEncerrados'));
    }

    public function jogar($id)
    {
        $bolao = $this->bolaoService->find($id);
        return view('bolao.jogar', compact('bolao'));
    }

    public function salvar(Request $request)
    {
        try {
            $data = $request->except('_token');

            $data['vendedor_id'] = auth()->id();

            $aposta = $this->apostasService->create($data);

            return redirect()->route('bolao.bilhete', ['id' => $aposta->id])
                ->with('success', 'Bilhete criado com sucesso!');

        } catch (\Throwable $exception) {
            $request->flashExcept('_token');

            return redirect()->back()
                ->with('error', "Ocorreu algum erro ao salvar! {$exception->getMessage()}");
        }
    }

    public function bilhete($id)
    {
        try {
            $aposta = $this->apostasService->find($id);

            $aposta->load([
                'vendedor',
                'palpites',
                'bolao',
                'bolao.partidas',
            ]);

            return view('bolao.bilhete', compact('aposta'));

        } catch (\Throwable $exception) {
            return redirect()->back()
                ->with('error', "Ocorreu algum erro ao abrir o bilhete! {$exception->getMessage()}");
        }
    }

    public function bilheteJson($id)
    {
        try {
            $aposta = $this->apostasService->find($id);

            $aposta->load([
                'vendedor',
                'palpites',
                'bolao',
                'bolao.partidas',
            ]);

            return response()->json([
                'aposta' => $aposta
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Erro ao recuparar dados do bilhete'
            ], 400);
        }
    }

    public function segundaVia()
    {
        return view('bolao.segundavia');
    }

    public function buscarSegundaVia()
    {
        try {
            $vendedorId = auth()->id();

            $apostas = $this->apostasService->buscarSegundaVia($vendedorId);

            return response()->json(['apostas' => $apostas]);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function cancelar($id)
    {
        try {
            $tempoParaCancelar = env('CANCEL_TIME', 0);

            if ($tempoParaCancelar <= 0) {
                throw new \Exception('Não é permitido cancelar apostas', 400);
            }

            $aposta = $this->apostasService->find($id);

            if ($aposta->vendedor_id !== auth()->id()) {
                throw new \Exception('Você não pode acessar essa aposta, pois ela pertence a outro cambista', 401);
            }

            $now = \Carbon\Carbon::now()->toDateTimeString();

            if ($aposta->created_at->addMinutes($tempoParaCancelar) <= $now) {
                throw new \Exception('O tempo limite para cancelamento foi excedido');
            }

            if ($aposta->bolao->data_limite < $now) {
                throw new \Exception("O bolão já iniciou, não pode mais cancelar essa aposta.");
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

    public function resultado($id)
    {
        $bolao = $this->bolaoService->buscarVencedores($id);

        return view('bolao.resultado', compact('bolao'));
    }
}
