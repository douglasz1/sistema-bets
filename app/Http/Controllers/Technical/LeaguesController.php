<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Http\Requests\LeaguesRequest;
use Bets\Services\LeaguesService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaguesController extends Controller
{
    /**
     * @var LeaguesService
     */
    private $leaguesService;

    public function __construct(LeaguesService $leaguesService)
    {
        $this->leaguesService = $leaguesService;
    }

    public function index()
    {
        return view('technical.leagues.index');
    }

    public function edit($id)
    {
        $league = $this->leaguesService->findById($id);

        return view('technical.leagues.edit', compact('league'));
    }

    public function update(LeaguesRequest $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        if ($request->hasFile('flag') && $request->file('flag')->isValid()) {
            $data['flag'] = $request->flag->store('flags', 'public');
        }

        $this->leaguesService->update($data, $id);

        return redirect()->route('technical.leagues.index')
            ->with('success', 'Liga alterada com sucesso!');
    }

    public function changeStatus($id)
    {
        $league = $this->leaguesService->alternateStatus($id);

        $msg = $league->active ? 'Liga ativada com sucesso!' : 'Liga desativada com sucesso!';

        return redirect()->route('technical.leagues.index')->with('success', $msg);
    }

    public function all()
    {
        $leagues = $this->leaguesService->all();

        $leagues->each(function ($item) {
            $item['edit_link'] = route('technical.leagues.edit', ['id' => $item->id]);
            $item['change_status'] = route('technical.leagues.status', ['id' => $item->id]);
        });

        return compact('leagues');
    }

    public function pluckByMatches(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();

        $filters = array(
            'start_date' => $request->has('start_date')
                ? $request->get('start_date') : $nowDate,

            'end_date' => $request->has('end_date')
                ? $request->get('end_date') : $nowDate,

            'select' => ['league_id AS value', 'name AS label']
        );

        $leagues = $this->leaguesService->getByFilters($filters);

        return compact('leagues');
    }
}
