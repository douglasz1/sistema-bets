<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Http\Requests\MatchesRequest;
use Bets\Services\LeaguesService;
use Bets\Services\MatchesService;
use Bets\Services\QuotationsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    /**
     * @var QuotationsService
     */
    private $quotationsService;
    /**
     * @var MatchesService
     */
    private $matchesService;
    /**
     * @var LeaguesService
     */
    private $leaguesService;

    public function __construct(QuotationsService $quotationsService, MatchesService $matchesService, LeaguesService $leaguesService)
    {
        $this->quotationsService = $quotationsService;
        $this->matchesService = $matchesService;
        $this->leaguesService = $leaguesService;
    }

    public function index()
    {
        return view('technical.matches.index');
    }

    public function edit($id)
    {
        $leagues = $this->leaguesService->pluckWithLeagueId();
        $match = $this->matchesService->findById($id);

        return view('technical.matches.edit', compact('match', 'leagues'));
    }

    public function update(MatchesRequest $request, $id)
    {
        $data = $request->all();

        unset($data["_token"]);

        $this->matchesService->update($data, $id);

        return redirect()->route('technical.matches.index')->with('success', 'Partida alterada com sucesso!');
    }

    public function changeStatus($id)
    {
        $match = $this->matchesService->alternateStatus($id);

        $msg = $match->active ? 'Partida ativada com sucesso!' : 'Partida desativada com sucesso!';

        return redirect()->route('technical.matches.index')->with('success', $msg);
    }

    public function all(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();

        $filters = array(
            'start_date' => $request->has('start_date')
                ? $request->get('start_date')
                : $nowDate,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date')
                : $nowDate,
            'league_id' => $request->has('league_id')
                ? $request->get('league_id')
                : null
        );

        $matches = $this->matchesService->getByFilters($filters);

        $matches->each(function ($item) {
            $item['match_name'] = $item->matchName();
            $item['human_date'] = $item->match_date->format('d/m H:i');
            $item['edit_link'] = route('technical.matches.edit', ['id' => $item->id]);
            $item['change_status'] = route('technical.matches.status', ['id' => $item->id]);
        });

        return compact('matches');
    }
}
