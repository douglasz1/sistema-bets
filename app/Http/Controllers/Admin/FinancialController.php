<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Http\Controllers\Controller;
use Bets\Services\Admin\UsersService;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    /**
     * @var UsersService
     */
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function index()
    {
        return view('admin.financial.index');
    }

    public function report(Request $request)
    {
        try {
            $startDate = $request->has('startDate')
                ? $request->get('startDate') : null;

            $finalDate = $request->has('finalDate')
                ? $request->get('finalDate') : null;

            $companyId = $request->has('companyId')
                ? $request->get('companyId') : null;
            $companyId = $companyId <= 0 ? null : $companyId;

            $managerId = $request->has('managerId')
                ? $request->get('managerId') : null;
            $managerId = $managerId <= 0 ? null : $managerId;

            $sellerId = $request->has('sellerId')
                ? $request->get('sellerId') : null;
            $sellerId = $sellerId <= 0 ? null : $sellerId;

            $usersHasBets = $this->usersService
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->setCompanyID($companyId)
                ->setManager($managerId)
                ->setSellers($sellerId)
                ->getHasBets();

            $usersDoesntHaveBets = $this->usersService
                ->setStartDate($startDate)
                ->setFinalDate($finalDate)
                ->setCompanyID($companyId)
                ->getDoesntHaveBets();

            $companies = [];

            foreach ($usersHasBets->groupBy('company_id') as $usersByCompany) {

                $sellers = $usersByCompany->where('manager', '!=', null);
                $managers = $usersByCompany->where('manager', null);

                $managersList = $this->createManagersList($managers, $sellers);

                $company = $sellers->first()->company ?? $managers->first()->company;

                $sellersDoesntHaveBets = $usersDoesntHaveBets->where('company_id', $company->id);

                $companies[] = [
                    'company' => $company->toArray(),
                    'managers' => $managersList,
                    'sellersNotBet' => $sellersDoesntHaveBets->toArray()
                ];

            }

            return response()->json([
                'companies' => array_values(array_sort($companies, function ($item) {
                    return $item['company']['name'];
                }))
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao recuparar dados do relatório'
            ], 400);
        }
    }

    private function createManagersList($managers, $sellers): array
    {
        $arrayManagers = [];

        foreach ($sellers->groupBy('user_id') as $sellersByManager) {
            $tempManager = $sellersByManager->first()->manager;

            $arrayManagers[] = [
                'manager' => [
                    'id' => $tempManager->id,
                    'name' => $tempManager->name,
                    'user_id' => $tempManager->user_id,
                    'company_id' => $tempManager->company_id,
                    'profitPercentage' => $tempManager->profit_percentage,
                    'managerCommission' => $tempManager->manager_commission
                ],
                'sellers' => $this->createSellersList($sellersByManager),
            ];
        }

        foreach ($managers as $manager) {
            $exists = array_first($arrayManagers, function ($item) use ($manager) {
                return $manager->id === (int)$item['manager']['id'];
            });

            if (!$exists) {
                $arrayManagers[] = [
                    'manager' => [
                        'id' => $manager->id,
                        'name' => $manager->name,
                        'user_id' => $manager->user_id,
                        'company_id' => $manager->company_id,
                        'profitPercentage' => $manager->profit_percentage,
                        'managerCommission' => $manager->manager_commission
                    ],
                ];
            }
        }

        return $this->remapManagers($arrayManagers, $managers);
    }

    private function createSellersList($sellers): array
    {
        $returnData = [];

        foreach ($sellers as $seller) {
            $entradaBolao = $seller->apostasBolao->sum('valor');
            $comissaoBolao = $seller->apostasBolao->sum('comissao');

            $prize = $seller->bets->where('status', 'win')->sum('prize');
            $betsValue = $seller->bets->where('status', '<>', 'canceled')->sum('bet_value');
            $commission = $seller->bets->where('status', '<>', 'canceled')->sum('commission');
            $expenses = $seller->expenses->sum('value');
            $payments = $seller->payments->sum('value');

            $subtotal = ($betsValue + $entradaBolao) - $commission - $prize - $comissaoBolao - $expenses;
            $profitCommission = 0;

            if ($subtotal > 0) {
                $profitCommission = $subtotal * ($seller->profit_percentage / 100);
            }

            $subtotal -= $profitCommission;
            $total = $subtotal + $payments;

            $commissions = $commission + $profitCommission;

            $betsValue = number_format($betsValue, 2, '.', '');
            $prize = number_format($prize, 2, '.', '');
            $commissions = number_format($commissions, 2, '.', '');
            $expenses = number_format($expenses, 2, '.', '');
            $payments = number_format($payments, 2, '.', '');
            $subtotal = number_format($subtotal, 2, '.', '');
            $total = number_format($total, 2, '.', '');

            $returnData[] = [
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'profitPercentage' => $seller->profit_percentage,
                    'managerCommission' => $seller->manager_commission,
                ],
                'bolaoQtd' => $seller->apostasBolao->count(),
                'entradaBolao' => $entradaBolao,
                'comissaoBolao' => $comissaoBolao,
                'betsQty' => $seller->bets->count(),
                'betsValue' => (float)$betsValue,
                'winnersQty' => $seller->bets->where('status', 'win')->count(),
                'winnersValue' => (float)$prize,
                'commissions' => (float)$commissions,
                'expenses' => (float)$expenses,
                'payments' => (float)$payments,
                'subtotal' => (float)$subtotal,
                'total' => (float)$total,
            ];
        }

        return $returnData;
    }

    private function remapManagers($arrayManagers, $managers): array
    {
        $returnData = array_map(function ($item) use ($managers) {

            if (!empty($managers)) {
                $value = array_first($managers, function ($value) use ($item) {
                    return $item['manager']['id'] === $value->id;
                });

                if (!empty($value)) {
                    $temp = $this->createSellersList([$value]);

                    if (isset($item['sellers'])) {
                        $item['sellers'] = array_merge($item['sellers'], $temp);
                    } else {
                        $item['sellers'] = $temp;
                    }
                }
            }

            $item['sellers'] = array_values(array_sort($item['sellers'], function ($seller) {
                return $seller['seller']['name'];
            }));

            $item['summary'] = $this->createSummary($item['sellers'], $item['manager']['managerCommission']);

            return $item;

        }, $arrayManagers);

        $returnData = array_values(array_sort($returnData, function ($manager) {
            return $manager['manager']['name'];
        }));

        return $returnData;
    }

    private function createSummary(array $sellers, $managerCommission): array
    {
        $summary = [
            'bolaoQtd' => 0,
            'entradaBolao' => 0,
            'comissaoBolao' => 0,
            'betsQty' => 0,
            'betsValue' => 0,
            'winnersQty' => 0,
            'winnersValue' => 0,
            'commissions' => 0,
            'expenses' => 0,
            'payments' => 0,
            'subtotal' => 0,
            'profitCommission' => 0,
            'managerCommission' => 0,
            'managerUse' => 0,
            'total' => 0,
        ];

        foreach ($sellers as $seller) {
            /*
             * Updating summary values
             */
            $summary['bolaoQtd'] += (int)$seller['bolaoQtd'];
            $summary['entradaBolao'] += (float)$seller['entradaBolao'];
            $summary['comissaoBolao'] += (float)$seller['comissaoBolao'];
            $summary['betsQty'] += (int)$seller['betsQty'];
            $summary['betsValue'] += (float)$seller['betsValue'];
            $summary['winnersQty'] += (int)$seller['winnersQty'];
            $summary['winnersValue'] += (float)$seller['winnersValue'];
            $summary['commissions'] += (float)$seller['commissions'];
            $summary['expenses'] += (float)$seller['expenses'];
            $summary['payments'] += (float)$seller['payments'];
            $summary['subtotal'] += (float)$seller['subtotal'];
            $summary['total'] += (float)$seller['total'];
        }

        $summary['betsValue'] = (float)number_format($summary['betsValue'], 2, '.', '');
        $summary['winnersValue'] = (float)number_format($summary['winnersValue'], 2, '.', '');
        $summary['commissions'] = (float)number_format($summary['commissions'], 2, '.', '');
        $summary['expenses'] = (float)number_format($summary['expenses'], 2, '.', '');
        $summary['payments'] = (float)number_format($summary['payments'], 2, '.', '');
        $summary['subtotal'] = (float)number_format($summary['subtotal'], 2, '.', '');
        $summary['total'] = (float)number_format($summary['total'], 2, '.', '');

        if ($summary['subtotal'] > 0) {
            $summary['managerCommission'] = $summary['subtotal'] * ($managerCommission / 100);
            $summary['managerCommission'] = (float)number_format($summary['managerCommission'], 2, '.', '');
        }

        $summary['total'] -= (float)$summary['managerCommission'];

        try {
            $subtotal = $summary['subtotal'] - $summary['managerCommission'];
            $entradas = $summary['betsValue'] + $summary['entradaBolao'];
            $summary['managerUse'] = ($subtotal / $entradas) * 100;
        } catch (\Throwable $e) {
            $summary['managerUse'] = 0;
        }

        $summary['managerUse'] = (int)number_format($summary['managerUse'], 0);

        return $summary;
    }
}
