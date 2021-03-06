<?php

namespace Bets\Http\Controllers\Manager;

use Bets\Http\Controllers\Controller;
use Bets\Services\BetsService;
use Bets\Services\Manager\UsersService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialController extends Controller
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
        return view('manager.financial.summary');
    }

    public function summaryReport(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();
        $filters = array(
            'start_date' => $request->has('start_date')
                ? $request->get('start_date') : $nowDate,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date') : $nowDate,
            'status' => $request->has('status')
                ? $request->get('status') : 'all',
            'sellers' => auth()->id(),
        );

        $bets = $this->betsService->getByFilters($filters);

        $profit_percentage = $bets->count() > 0 ? $bets->first()->seller->profit_percentage : 0;

        return compact('profit_percentage', 'bets');
    }

    public function general()
    {
        return view('manager.financial.general');
    }

    public function generalReport(Request $request)
    {
        try {
            $startDate = $request->has('startDate')
                ? $request->get('startDate') : null;

            $finalDate = $request->has('finalDate')
                ? $request->get('finalDate') : null;

            $managerId = auth()->id();

            $sellerId = $request->has('sellerId')
                ? $request->get('sellerId') : null;

            $usersHasBets = $this->usersService
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->setManager($managerId)
                ->setSellers($sellerId)
                ->getHasBets();

            $usersDoesntHaveBets = $this->usersService
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->getDoesntHaveBets();

            $sellers = [];

            foreach ($usersHasBets->sortBy('seller.name') as $seller) {
                $entradaBolao = $seller->apostasBolao->sum('valor');
                $comissaoBolao = $seller->apostasBolao->sum('comissao');

                $prize = $seller->bets->where('status', 'win')->sum('prize');
                $betsValue = $seller->bets->where('status', '<>', 'canceled')->sum('bet_value');
                $commission = $seller->bets->where('status', '<>', 'canceled')->sum('commission');
                $expenses = $seller->expenses->sum('value');
                $payments = $seller->payments->sum('value');

                $subtotal = $betsValue - $commission - $prize - $expenses;
                $bolaoSubtotal = $entradaBolao - $comissaoBolao;

                $profitCommission = 0;
                $comissaoLucroBolao = 0;

                if ($subtotal > 0) {
                    $profitCommission = $subtotal * ($seller->profit_percentage / 100);
                }

                if ($bolaoSubtotal > 0) {
                    $comissaoLucroBolao = $bolaoSubtotal * ($seller->profit_percentage / 100);
                }

                $total = $subtotal - $profitCommission;
                $totalBolao = $bolaoSubtotal - $comissaoLucroBolao;
                $totalGeral = $total + $totalBolao ;

                $betsValue = number_format($betsValue, 2, '.', '');
                $prize = number_format($prize, 2, '.', '');
                $commission = number_format($commission, 2, '.', '');
                $profitCommission = number_format($profitCommission, 2, '.', '');
                $expenses = number_format($expenses, 2, '.', '');
                $payments = number_format($payments, 2, '.', '');
                $subtotal = number_format($subtotal, 2, '.', '');
                $total = number_format($total, 2, '.', '');

                $sellers[] = [
                    'seller' => [
                        'id' => $seller->id,
                        'name' => $seller->name,
                        'profitPercentage' => $seller->profit_percentage,
                    ],
                    'entradaBolao' => $entradaBolao,
                    'comissaoBolao' => $comissaoBolao,
                    'bolaoSubtotal' => $bolaoSubtotal,
                    'comissaoLucroBolao' => $comissaoLucroBolao,
                    'totalBolao' => $totalBolao,
                    'betsQty' => $seller->bets->count(),
                    'betsValue' => (float)$betsValue,
                    'winnersQty' => $seller->bets->where('status', 'win')->count(),
                    'winnersValue' => (float)$prize,
                    'commission' => (float)$commission,
                    'expenses' => (float)$expenses,
                    'subtotal' => (float)$subtotal,
                    'payments' => (float)$payments,
                    'profitCommission' => (float)$profitCommission,
                    'total' => (float)$total,
                    'totalGeral' => $totalGeral,
                ];
            }

            $manager = auth()->user();

            return response()->json([
                'manager' => $manager,
                'sellers' => $sellers,
                'sellersNotBet' => $usersDoesntHaveBets->toArray()
            ]);

        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
