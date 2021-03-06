<?php

namespace Bets\Http\Controllers;


use Bets\Services\ResultsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    private $service;

    public function __construct(ResultsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $request->flash();

        $filters = [
            'start_date' => Carbon::now()->subDay(3)->toDateString(),
            'end_date' => Carbon::now()->toDateString(),
            'status' => 'finished',
        ];

        $results = $this->service->getByFilters($filters);

        return view('results.index', compact('results'));
    }
}
