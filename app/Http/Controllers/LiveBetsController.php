<?php

namespace Bets\Http\Controllers;

use Bets\Http\Requests\BetsStoreRequest;
use Bets\Services\BetsService;
use Bets\Services\LiveMatchesService;
use Bets\Models\Client;

class LiveBetsController extends Controller
{
    /**
     * @var LiveMatchesService
     */
    private $liveMatchesService;
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(LiveMatchesService $liveMatchesService, BetsService $betsService)
    {
        $this->liveMatchesService = $liveMatchesService;
        $this->betsService = $betsService;
    }

    public function index()
    {
        $usuario = auth()->user();

        if (!$usuario->ao_vivo) {
            return redirect('/home');
        }

        return view('bets.live');
    }

    public function matches()
    {
        try {
            $banca = \Bets\Models\Banca::query()
                    ->select('partidas_inativas_live', 'ligas_inativas_live')
                    ->first();

            return response()->json([
                'partidas_inativas' => array_values($banca->partidas_inativas_live),
                'ligas_inativas' => array_values($banca->ligas_inativas_live),
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'partidas_inativas' => [],
                'ligas_inativas' => [],
                'message' => $exception->getMessage()
            ], 404);
        }
    }

    public function store(BetsStoreRequest $request)
    {
        try {
            $data['client_name'] = Client::find($request->get('name'))->name;
            $data['client_id'] = $request->get('name');
            $data['bet_value'] = moneyUS($request->get('bet_value'));
            $data['quotations'] = $request->get('choices');

             $bet = $this->betsService->create($data, true);

            $bet->load([
                'tips',
                'tips.match',
                'tips.match.league',
                'tips.match.league.country',
                'tips.match.sport',
                'seller' => function ($query) {
                    return $query->select('name', 'id', 'company_id', 'username', 'percentual_premio');
                },
                'seller.company' => function ($query) {
                    return $query->select('print_name AS name', 'id');
                }
            ]);

            $bet['print_url'] = route('bets.printing', ['id' => $bet->id]);
            $bet['bet_date'] = $bet->created_at->format("d/m H:i");

            $bet->tips->each(function ($item) {
                $item->match['human_date'] = $item->match->match_date->format("d/m H:i");
                $item->match['match_name'] = $item->match->matchName();
            });

            return response()->json(['bet' => $bet]);

        } catch (\Throwable $exception) {
            return response()->json([
                'choices' => [$exception->getMessage()],
            ], 400);
        }
    }
}
