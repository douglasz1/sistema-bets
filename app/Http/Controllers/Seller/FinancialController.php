<?php

namespace Bets\Http\Controllers\Seller;

use Bets\Services\Seller\UsersService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class FinancialController extends Controller
{
    /**
     * @var UsersService
     */
    private $service;

    public function __construct(UsersService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('seller.financial');
    }

    public function report(Request $request)
    {
        try {
            $startDate = $request->has('startDate')
                ? $request->get('startDate') : null;

            $finalDate = $request->has('endDate')
                ? $request->get('endDate') : null;

            $clientId = $request->has('clientId')
                ? $request->get('clientId') : null;

            $sellerId = auth()->id();

            $userWithBets = $this->service
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->setSeller($sellerId)
                ->setClient($clientId)
                ->getHasBets()->first();

            $bets = $this->createSummary($userWithBets);

            return response()->json([
                'seller' => [
                    'id' => $userWithBets->id,
                    'name' => $userWithBets->name,
                    'profitPercentage' => $userWithBets->profit_percentage,
                ],
                'bets' => $bets,
            ]);

        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    private function createSummary($userWithBets)
    {
        $profitPercentage = $userWithBets->profit_percentage;
        $apostasBets = $userWithBets->bets;
        $apostasBolao = $userWithBets->apostasBolao;

        $expenses = $userWithBets->expenses->sum('value');
        $payments = $userWithBets->payments->sum('value');

        $entradaBolao = $apostasBolao->sum('valor');
        $comissaoBolao = $apostasBolao->sum('comissao');

        $prize = $apostasBets->where('status', 'win')->sum('prize');
        $betsValue = $apostasBets->where('status', '<>', 'canceled')->sum('bet_value');
        $commission = $apostasBets->where('status', '<>', 'canceled')->sum('commission');

        $subtotal = $betsValue - $commission - $prize - $expenses;
        $bolaoSubtotal = $entradaBolao - $comissaoBolao;

        $profitCommission = 0;

        if ($subtotal > 0 && $profitPercentage > 0) {
            $profitCommission = $subtotal * ($profitPercentage / 100);
            $subtotal = $subtotal - $profitCommission;
        }

        $total = $subtotal + $payments;

        $betsValue = number_format($betsValue, 2, '.', '');
        $prize = number_format($prize, 2, '.', '');
        $commission = number_format($commission, 2, '.', '');
        $profitCommission = number_format($profitCommission, 2, '.', '');
        $expenses = number_format($expenses, 2, '.', '');
        $payments = number_format($payments, 2, '.', '');
        $subtotal = number_format($subtotal, 2, '.', '');
        $total = number_format($total, 2, '.', '');

        return [
            'entradaBolao' => $entradaBolao,
            'comissaoBolao' => $comissaoBolao,
            'bolaoSubtotal' => $bolaoSubtotal,
            'betsValue' => (float)$betsValue,
            'winnersValue' => (float)$prize,
            'commission' => (float)$commission,
            'expenses' => (float)$expenses,
            'payments' => (float)$payments,
            'subtotal' => (float)$subtotal,
            'profitCommission' => (float)$profitCommission,
            'total' => (float)$total,
        ];
    }
}
