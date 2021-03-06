<?php

namespace Bets\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class CancelarPalpitesController extends Controller
{
    public function index()
    {
        return view('admin.palpites.cancelar');
    }

    public function buscar(Request $request)
    {
        try {
            $pesquisa = $request->get('pesquisa');

            $partidas = \Bets\Models\Result::query()
                ->where(function($query) use ($pesquisa) {
                    $query->where('home_team', 'like', "%{$pesquisa}%")
                        ->orWhere('away_team', 'like', "%{$pesquisa}%");
                })
                ->orderByDesc('match_date')
                ->with('league', 'league.country')
                ->take(10)
                ->get();

            return response()->json([
                'partidas' => $partidas,
            ]);

        } catch(\Throwable $exception) {
            return response()->json([
                'mensagem' => 'Nenhuma partida encontrada',
                'erro' => $exception->getMessage(),
            ], 404);
        }
    }

    public function cancelar(Request $request)
    {
        try {
            $partida = $request->get('partida');
            $cancelarPor = $request->get('cancelar_por');
            $dataCancelar = $request->get('data_cancelar');

            $service = app()->make('Bets\Services\TipsService');

            $query = \Bets\Models\Tip::query();

            $query->where('match_id', $partida);

            if ($cancelarPor === 'data') {
                $query->where('created_at', '>=', $dataCancelar);
            }

            $query->with('bet')->chunkById(50, function ($palpites) use ($service) {
                foreach($palpites as $palpite) {
                    if(!is_null($palpite->bet->seller_id) && $palpite->bet->origin === 'site') {
                        $service->cancel($palpite->id);
                    }
                }
            });

            return response()->json('Palpites serão cancelados!');

        } catch (\Throwable $exception) {
            return response()->json([
                'mensagem' => 'Erro ao cancelar',
                'erro' => $exception->getMessage(),
            ], 400);
        }
    }

    public function cancelados(Request $request)
    {
        $palpites = \Bets\Models\Tip::query()
            ->where('status', 'canceled')
            ->orderByDesc('updated_at')
            ->with('bet', 'match', 'match.league', 'match.league.country')
            ->paginate(50);

        return view('admin.palpites.cancelados', compact('palpites'));
    }
}
