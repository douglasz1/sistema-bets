<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Services\Admin\GoalsService;
use Illuminate\Http\Request;

class GoalsController extends Controller
{
    /**
     * @var GoalsService
     */
    private $service;

    public function __construct(GoalsService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('admin.goals.index');
    }

    public function all(Request $request)
    {
        try {
            $startDate = $request->has('startDate')
                ? $request->get('startDate') : null;

            $finalDate = $request->has('finalDate')
                ? $request->get('finalDate') : null;

            $sellers = $this->service
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->getSellers();

            $sellersWithBets = [];
            $sellersWithoutBets = [];

            foreach ($sellers as $seller) {
                $tempSeller = [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'company' => $seller->company->name,
                    'sales_goal' => (float)number_format($seller->sales_goal, 2, '.', ''),
                    'bets_value' => 0,
                    'goal_percentage' => 0
                ];

                if ($seller->bets->count() > 0) {
                    $tempSeller['bets_value'] = $seller->bets->sum('bet_value');
                    $tempSeller['bets_value'] = (float)number_format($tempSeller['bets_value'], 2, '.', '');

                    if ($tempSeller['sales_goal'] > 0) {
                        $tempSeller['goal_percentage'] = round(($tempSeller['bets_value'] / $tempSeller['sales_goal']) * 100, 0);

                        $tempSeller['goal_percentage'] = $tempSeller['goal_percentage'] > 100 ? 100 : $tempSeller['goal_percentage'];
                    }

                    $sellersWithBets[] = $tempSeller;
                } else {
                    $sellersWithoutBets[] = $tempSeller;
                }
            }

            return response()->json([
                'sellersWithBets' => $sellersWithBets,
                'sellersWithoutBets' => $sellersWithoutBets
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar cambistas',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
