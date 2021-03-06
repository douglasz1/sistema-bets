<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Requests\LeaguesRequest;
use Bets\Models\Country;
use Bets\Services\LeaguesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

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
        return view('admin.leagues.index');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function edit($id)
    {
        $league = $this->leaguesService->findById($id);
        $countries = Country::query()->orderBy('name')->pluck('name', 'id');

        return view('admin.leagues.edit', compact('league', 'countries'));
    }

    public function update(LeaguesRequest $request, $id)
    {
        $data = $request->only(['name', 'order', 'country_id']);

        $this->leaguesService->update($data, $id);

        return redirect()->route('admin.leagues.index')
            ->with('success', 'Liga alterada com sucesso!');
    }

    public function remove($id)
    {
    }

    public function changeStatus(Request $request, $id)
    {
        $tipo = $request->filled('tipo') ? $request->get('tipo') : 'pre';

        $liga = $this->leaguesService->alternateStatus($id, $tipo);

        return response()->json([
            'liga' => $liga
        ]);
    }

    public function all()
    {
        $ligas = $this->leaguesService->all();

        $banca = \Bets\Models\Banca::query()->first();

        $preJogoInativas = array_values($banca->ligas_inativas_pre);
        $AoVivoInativas = array_values($banca->ligas_inativas_live);

        return response()->json([
            'ligas' => $ligas,
            'inativas_pre' => $preJogoInativas,
            'inativas_live' => $AoVivoInativas,
        ]);
    }

    public function pluckByMatches(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();

        $filters = [
            'start_date' => $request->filled('start_date')
                ? $request->get('start_date') : $nowDate,

            'end_date' => $request->filled('end_date')
                ? $request->get('end_date') : $nowDate,

            'select' => ['league_id AS value', 'name AS label', 'country_id']
        ];

        $leagues = $this->leaguesService->getByFilters($filters);

        return compact('leagues');
    }
}
