<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Requests\MatchesRequest;
use Bets\Services\LeaguesService;
use Bets\Services\MatchesService;
use Bets\Services\QuotationsService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

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
        return view('admin.matches.index');
    }

    public function edit($id)
    {
        $leagues = $this->leaguesService->pluckWithLeagueId();
        $match = $this->matchesService->findById($id);

        return view('admin.matches.edit', compact('match', 'leagues'));
    }

    public function update(MatchesRequest $request, $id)
    {
        $data = $request->all();

        unset($data["_token"]);

        $this->matchesService->update($data, $id);

        return redirect()->route('admin.quotations.index')->with('success', 'Partida alterada com sucesso!');
    }

    public function changeStatus($id)
    {
        $match = $this->matchesService->alternateStatus($id);

        $msg = $match->active ? 'Partida ativada com sucesso!' : 'Partida desativada com sucesso!';

        return redirect()->route('admin.quotations.index')->with('success', $msg);
    }

    public function all(Request $request)
    {
        $filters = [
            'start_date' => $request->has('start_date')
                ? $request->get('start_date') : null,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date') : null,
            'sport_id' => $request->has('sport_id')
                ? $request->get('sport_id') : 1
        ];

        $matches = $this->matchesService->getByFilters($filters);

        $matches->each(function ($item) {
            $item['match_name'] = $item->matchName();
            $item['human_date'] = $item->match_date->format('d/m H:i');
            $item['edit_link'] = route('admin.matches.edit', ['id' => $item->id]);
            $item['change_status'] = route('admin.matches.status', ['id' => $item->id]);
        });

        return compact('matches');
    }
}
