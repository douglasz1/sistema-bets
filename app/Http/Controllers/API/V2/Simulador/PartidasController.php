<?php

namespace Bets\Http\Controllers\API\V2\Simulador;

use Bets\Http\Resources\Simulador\LigaResource;
use Bets\Http\Resources\Simulador\PartidaResource;
use Bets\Services\Simulador\PartidasService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class PartidasController extends Controller
{
    /**
     * @var PartidasService
     */
    private $partidasService;

    public function __construct(PartidasService $partidasService)
    {
        $this->partidasService = $partidasService;
    }

    public function partidas(Request $request)
    {
        try {
            $data = $request->filled('data') ? $request->get('data') : 'hoje';

            $esporte = $request->filled('esporte')
                ? $request->get('esporte') : 1;

            $liga = $request->filled('liga')
                ? $request->get('liga') : null;

            $usuario = auth('api')->user();

            $modificarCotacao = $usuario->quotationModifier() + ($usuario->company->quotation_modifier / 100);

            $eventosDoDia = $this->partidasService
                ->setData($data)
                ->setEsporte($esporte)
                ->setLiga($liga)
                ->buscarPartidas();

            $eventos = collect([]);

            foreach ($eventosDoDia->groupBy('league_id') as $eventosPorLiga) {
                $liga = $eventosPorLiga->pluck('league')->first()->toArray();

                $eventosTemp = collect([]);

                foreach ($eventosPorLiga as $partida) {
                    if (is_null($partida->cotacoesPrincipais) || count($partida->cotacoesPrincipais) < 2) continue;

                    $cotacoes = array_map(function ($cotacao) use ($modificarCotacao, $partida) {
                        $cotacao['value'] = $cotacao['value'] + ($cotacao['value'] * $modificarCotacao);
                        $cotacao['value'] = number_format($cotacao['value'], 2);

                        if ($cotacao['value'] < 1.01) {
                            $cotacao['value'] = 1.01;
                        } elseif ($cotacao['value'] > 100) {
                            $cotacao['value'] = 100;
                        }

                        $cotacao['match_id'] = $partida->match_id;

                        return $cotacao;
                    }, $partida->cotacoesPrincipais->toArray());

                    $partida['cotacoes_principais'] = collect($cotacoes);

                    unset($partida['league']);
                    unset($partida['sport']);

                    $eventosTemp->push($partida);
                }

                $liga['partidas'] = PartidaResource::collection($eventosTemp);

                $item = LigaResource::make($liga)->toArray($liga);

                $eventos->push($item);
            }

            $eventos = collect(['eventos' => $eventos]);

            return response(gzcompress($eventos->toJson(), 9))->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Content-type' => 'application/json; charset=utf-8',
                'Content-Encoding' => 'gzip'
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'eventos' => [],
                'mensagem' => 'Erro ao listar partidas',
                'erro' => $exception->getMessage(),
            ], 404);
        }
    }

    public function cotacoes($eventoID)
    {
        try {
            $evento = $this->partidasService->cotacoes($eventoID);

            $usuario = auth('api')->user();

            $modificarCotacao = $usuario->quotationModifier() + ($usuario->company->quotation_modifier / 100);

            $cotacoes = array_map(function ($cotacao) use ($modificarCotacao, $evento) {
                $cotacao['value'] = $cotacao['value'] + ($cotacao['value'] * $modificarCotacao);
                $cotacao['value'] = number_format($cotacao['value'], 2);

                if ($cotacao['value'] < 1.01) {
                    $cotacao['value'] = 1.01;
                } elseif ($cotacao['value'] > 100) {
                    $cotacao['value'] = 100;
                }

                $cotacao['match_id'] = $evento->match_id;

                return $cotacao;
            }, $evento->quotations->toArray());

            $evento['cotacoes'] = collect($cotacoes);

            $evento = collect(new PartidaResource($evento));

            $evento = collect(['evento' => $evento]);

            return response(gzcompress($evento->toJson(), 9))->withHeaders([
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Content-type' => 'application/json; charset=utf-8',
                'Content-Encoding' => 'gzip'
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'evento' => [],
                'mensagem' => 'Erro ao listar cotações',
                'erro' => $exception->getMessage(),
            ], 404);
        }
    }
}
