<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Services\Bolao\ApostasService;
use Bets\Services\Bolao\BolaoService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class BolaoController extends Controller
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
        $boloes = $this->bolaoService->todos();

        return view('technical.bolao.index', compact('boloes'));
    }

    public function criar()
    {
        return view('technical.bolao.criar');
    }

    public function salvar(Request $request)
    {
        try {
            $bolaoDados = $request->except('_token');

            $this->bolaoService->create($bolaoDados);

            return redirect()->route('technical.bolao.index')
                ->with('success', 'Bolão criado com sucesso!');

        } catch (\Throwable $exception) {
            $request->flashExcept('_token');

            return redirect()->back()
                ->with('error', "Ocorreu algum erro ao salvar! {$exception->getMessage()}");
        }
    }

    public function detalhes($id)
    {
        $bolao = $this->bolaoService->buscarVencedores($id);

        return view('technical.bolao.detalhes', compact('bolao'));
    }

    public function apostas($id)
    {
        $bolao = $this->bolaoService->find($id);

        $apostas = $this->apostasService->paginar($id);

        return view('technical.bolao.apostas', compact('bolao', 'apostas'));
    }

    public function placares($id)
    {
        $bolao = $this->bolaoService->find($id);

        return view('technical.bolao.placares', compact('bolao'));
    }

    public function salvarPlacares(Request $request, $id)
    {
        try {
            $data = $request->except('_token');

            $this->bolaoService->salvarPlacares($data, $id);

            return redirect()->route('technical.bolao.detalhes', ['id' => $id])
                ->with('success', 'Placares inseridos com sucesso!');

        } catch (\Throwable $exception) {
            $request->flashExcept('_token');

            return redirect()->back()
                ->with('error', "Ocorreu algum erro ao salvar! {$exception->getMessage()}");
        }
    }

    public function vencedor($id)
    {
        try {
            $this->bolaoService->calcularResultados($id);

            return redirect()->route('technical.bolao.detalhes', ['id' => $id])
                ->with('success', 'Resultado calculado com sucesso!');

        } catch (\Throwable $exception) {
            return redirect()->back()
                ->with('error', "Ocorreu algum erro ao salvar! {$exception->getMessage()}");
        }
    }
}
