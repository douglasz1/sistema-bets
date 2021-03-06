<?php

namespace Bets\Http\Controllers\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Bets\Models\{League, Sport};
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class JogosManuaisController extends Controller
{
    public function index(Request $request)
    {
        $esportes = Sport::where('active', true)->pluck('name', 'id');

        $esporte = $request->get('esporte', 1);

        $ligas = League::whereSportId($esporte)
            ->whereActive(true)
            ->whereManual(true)
            ->select(['name', 'id', 'country_id'])
            ->with('country')
            ->get();

        $ligas = $ligas->mapWithKeys(function ($liga) {
            return [$liga['id'] => $liga['country']['name'] . ": " . $liga['name']];
        });

        return view('admin.jogos-manuais.index', compact('esportes', 'ligas'));
    }

    public function salvarLiga(Request $request)
    {
        $pais = \Bets\Models\Country::firstOrCreate(['name' => $request->get('nome_pais')], ['api_id' => 0]);

        $liga = League::create([
            'name' => $request->get('nome_campeonato'),
            'flag' => "{$pais->api_id}.svg",
            'order' => 0,
            'manual' => 1,
            'league_id' => "{$pais->api_id}-" . Str::slug($request->get('nome_campeonato')),
            'sport_id' => $request->get('esporte'),
            'country_id' => $pais->id,
        ]);

        return redirect()->route('admin.jogos-manuais.index', [
            'esporte' => $request->get('esporte'),
            'campeonato' => $liga->id,
        ]);
    }

    public function salvarEvento(Request $request)
    {
        $validacao = validator()->make($request->all(), [
            'esporte' => 'required',
            'campeonato' => 'required',
            'data' => 'required',
            'time_casa' => 'required',
            'time_fora' => 'sometimes|required',
            'mercado' => 'sometimes|required',
        ]);

        if ($validacao->fails()) {
            return redirect()->route('admin.jogos-manuais.index', [
                'data' => $request->get('data'),
                'esporte' => $request->get('esporte'),
                'time_casa' => $request->get('time_casa'),
                'time_fora' => $request->get('time_fora'),
                'mercado' => $request->get('mercado'),
                'campeonato' => $request->get('campeonato'),
            ])->withErrors($validacao->errors());
        }

        $esporte = Sport::find($request->get('esporte'));

        $liga = League::find($request->get('campeonato'));

        $dataEvento = dateToDatabase($request->get('data'));

        $timestamp = now()->getTimestamp();

        $evento = \Bets\Models\Match::create([
            'match_id' => $timestamp,
            'league_id' => $liga->league_id,
            'sport_id' => $esporte->id,
            'sport_slug' => $esporte->slug,
            'home_team' => $request->get('time_casa'),
            'away_team' => $request->get('time_fora', ' '),
            'match_date' => $dataEvento,
            'quotations_qty' => 0,
            'active' => true,
        ]);

        \Bets\Models\Result::create([
            'match_id' => $timestamp,
            'league_id' => $liga->league_id,
            'sport_id' => $esporte->id,
            'sport_slug' => $esporte->slug,
            'home_team' => $request->get('time_casa'),
            'away_team' => $request->get('time_fora', ' '),
            'match_date' => $dataEvento,
        ]);

        $odds = [];

        if ($esporte->id == 7) {
            $competidores = $request->get('competidor');
            $cotacoes = $request->get('cotacao');

            $mercado = $request->get('mercado');

            foreach ($competidores as $indice => $competidor) {
                if (!empty($competidor)) {
                    $valor = str_replace(',', '.', $cotacoes[$indice]);

                    $odds[] = [
                        'match_id' => $evento->id,
                        'bet_name' => $mercado,
                        'bet_slug' => Str::slug($mercado),
                        'choice_name' => $competidor,
                        'choice_slug' => Str::slug($competidor),
                        'value' => floatval($valor),
                        'bet_order' => 100,
                        'choice_order' => 0,
                        'upgradable' => 0,
                    ];
                }
            }
        } else {
            $conteudo = file_get_contents(storage_path('app/categorias.json'));

            $categorias = json_decode($conteudo, true);

            foreach ($request->get('cotacao') as $mercado => $palpites) {
                foreach ($palpites as $palpite => $valor) {
                    if (!empty($valor)) {
                        $valor = str_replace(',', '.', $valor);

                        $categoria = Arr::first($categorias, function ($c) use ($mercado, $palpite) {
                            return "{$mercado}" === $c['mercado'] && "{$palpite}" === $c['palpite'];
                        });

                        $odds[] = [
                            'match_id' => $evento->id,
                            'bet_name' => $categoria['mercado_descricao'],
                            'bet_slug' => $categoria['mercado'],
                            'choice_name' => $categoria['palpite_descricao'],
                            'choice_slug' => $categoria['palpite'],
                            'value' => floatval($valor),
                            'bet_order' => 100,
                            'choice_order' => 0,
                            'upgradable' => 0,
                        ];
                    }
                }
            }
        }

        if (count($odds) > 0) {
            \Bets\Models\Quotation::insert($odds);

            $evento->quotations_qty = count($odds);

            $evento->save();
        }

        return redirect()
            ->route('admin.jogos-manuais.index')
            ->with('success', 'Evento inserido com sucesso.');
    }
}
