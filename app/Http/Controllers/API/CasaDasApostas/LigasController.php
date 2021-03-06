<?php

namespace Bets\Http\Controllers\API\CasaDasApostas;

use Bets\Models\League;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LigasController extends Controller
{
    /**
     * @var League
     */
    private $league;

    public function __construct(League $league)
    {
        $this->league = $league;
    }

    public function index()
    {
        $ligas = $this->league
            ->select(['league_id', 'country_id', 'name'])
            ->with(['country' => function ($query) {
                $query->select('id', 'name', 'api_id');
            }])
            ->where('active', true)
            ->whereHas('matches', function ($query) {
                $query->where('sport_slug', 'soccer')
                    ->where('match_date', '>', Carbon::now()->toDateTimeString())
                    ->where('active', true);
            })
            ->orderByRaw(DB::raw('sign(`order`) desc'))
            ->orderBy('order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();

        return response()->json($ligas);
    }

    public function abrir($id)
    {
        try {
            $liga = $this->league
                ->select(['league_id', 'country_id', 'name', 'sport_id'])
                ->with([
                    'country' => function ($query) {
                        $query->select('id', 'name', 'api_id');
                    },
                    'sport' => function ($query) {
                        $query->select('id', 'name', 'slug');
                    }
                ])
                ->findOrFail($id);

            return response()->json($liga->makeHidden(['sport_id']));

        } catch (\Throwable $exception) {
            return response()->json([
                'resultado' => 'Erro ao abrir campeonato'
            ], 400);
        }
    }
}
