<?php

namespace Bets\Http\Controllers\Manager;

use Bets\Http\Controllers\Controller;
use Bets\Services\BetsService;
use Bets\Services\UsersService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;
    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(BetsService $betsService, UsersService $usersService)
    {
        $this->betsService = $betsService;
        $this->usersService = $usersService;
    }

    public function summary()
    {
        return view('manager.cashier.summary');
    }

    public function summaryReport(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();
        $filters = [
            'start_date' => $request->has('start_date')
                ? $request->get('start_date')
                : $nowDate,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date')
                : $nowDate,
            'status' => $request->has('status')
                ? $request->get('status')
                : 'all',
            'sellers' => array(auth()->id())
        ];

        $bets = $this->betsService->getByFilters($filters);

        $bets->each(function ($item) {
            $item['human_date'] = $item->created_at->format('d/m/Y H:i:s');
        });

        return compact('bets');
    }

    public function monitoring()
    {
        return view('manager.cashier.monitoring');
    }

    public function monitoringReport(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();

        if ($request->has('seller_id') && (int)$request->get('seller_id') !== 0) {
            $sellers = (int)$request->get('seller_id');
        } else {
            $sellers = $this->usersService->getByFilters([
                'parent_id' => auth()->id(),
                'select' => 'id'
            ])->pluck('id')->toArray();

            $sellers[] = auth()->id();
        }

        $filters = [
            'start_date' => $request->has('start_date')
                ? $request->get('start_date')
                : $nowDate,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date')
                : $nowDate,
            'status' => $request->has('status')
                ? $request->get('status')
                : 'all',
            'sellers' => $sellers,
        ];

        $bets = $this->betsService->getByFilters($filters);

        $bets->each(function ($item) {
            $item['human_date'] = $item->created_at->format('d/m/Y H:i:s');
        });

        return compact('bets');
    }
}
