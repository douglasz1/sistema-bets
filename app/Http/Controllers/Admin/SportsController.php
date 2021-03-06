<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Services\SportsService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class SportsController extends Controller
{
    /**
     * @var SportsService
     */
    private $sportsService;

    public function __construct(SportsService $sportsService)
    {
        $this->sportsService = $sportsService;
    }

    public function all()
    {
        try {
            $sports = $this->sportsService->actives()->all();

            $sports = $sports->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

            return response()->json([
                'sports' => $sports,
            ]);

        } catch (\Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 400);
        }
    }
}
