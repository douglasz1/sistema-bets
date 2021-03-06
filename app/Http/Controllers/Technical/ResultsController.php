<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Http\Requests\ResultsUpdateRequest;
use Bets\Services\ResultsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    /**
     * @var ResultsService
     */
    private $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function index(Request $request)
    {
        $results = [];

        if ($request->has('start_date')) {
            $request->flash();

            $filters = array(
                'start_date' => $request->has('start_date')
                    ? $request->get('start_date')
                    : Carbon::now()->subDay(2)->toDateString(),
                'end_date' => $request->has('end_date')
                    ? $request->get('end_date')
                    : Carbon::now()->toDateString(),
                'status' => $request->has('status')
                    ? $request->get('status')
                    : 'pending',
            );

            $results = $this->resultsService->getByFilters($filters);
        }

        return view('technical.results.index', compact('results'));
    }

    public function store(Request $request)
    {
        $data = $request->get('results');

        foreach ($data as $key => $value) {
            if ($value['home_1st'] === '' || $value['away_1st'] === '') continue;
            if ($value['home_2nd'] === '' || $value['away_2nd'] === '') continue;
            if ($value['home_final'] === '' || $value['away_final'] === '') continue;

            $value['status'] = 'finished';
            $this->resultsService->update($value, $key);
        }

        return redirect()->route('technical.results.index')->with('success', 'Resutados cadastrados com sucesso. Os ganhadores serão calculados em breve...');
    }

    public function edit($id)
    {
        $result = $this->resultsService->findById($id);

        return view('technical.results.edit', compact('result'));
    }

    public function update(ResultsUpdateRequest $request, $id)
    {
        $data = $request->all();

        $this->resultsService->update($data, $id);

        $message = ($data['status'] === 'finished') ? 'Os ganhadores serão calculados em breve...' : '';

        return redirect()->route('technical.results.index')->with('success', 'Resutado cadastrado com sucesso. ' . $message);
    }

    public function cancel($id)
    {
        $this->resultsService->update(array('status' => 'canceled'), $id);

        return redirect()->route('technical.results.index')->with('success', 'Partida cancelada com sucesso!');
    }
}
