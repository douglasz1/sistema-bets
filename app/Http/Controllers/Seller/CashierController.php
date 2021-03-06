<?php

namespace Bets\Http\Controllers\Seller;

use Bets\Services\BetsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class CashierController extends Controller
{
    /**
     * @var BetsService
     */
    private $betsService;

    public function __construct(BetsService $betsService)
    {
        $this->betsService = $betsService;
    }

    public function index()
    {
        return view('seller.cashier');
    }

    public function report(Request $request)
    {
        $dateToday = Carbon::now()->toDateString();

        $filters = [
            'start_date' => $request->has('start_date')
                ? $request->get('start_date') : $dateToday,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date') : $dateToday,
            'status' => $request->has('status')
                ? $request->get('status') : 'all',
            'sellers' => auth()->id(),
        ];

        $bets = $this->betsService->getByFilters($filters);

        $bets->each(function ($item) {
            $item['ticket_url'] = route('seller.bets.summary', ['id' => $item->id]);
            $item['human_date'] = $item->created_at->format('d/m/Y H:i:s');
        });

        return compact('bets');
    }
}
