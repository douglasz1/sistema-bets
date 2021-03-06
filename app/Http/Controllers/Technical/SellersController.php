<?php

namespace Bets\Http\Controllers\Technical;


use Bets\Http\Controllers\Controller;
use Bets\Http\Requests\SellersCreateRequest;
use Bets\Http\Requests\SellersUpdateRequest;
use Bets\Services\Admin\ManagersService;
use Bets\Services\Admin\SellersService;
use Illuminate\Http\Request;

class SellersController extends Controller
{
    /**
     * @var SellersService
     */
    private $service;
    /**
     * @var ManagersService
     */
    private $managersService;

    public function __construct(SellersService $service, ManagersService $managersService)
    {
        $this->service = $service;
        $this->managersService = $managersService;
    }

    public function index()
    {
        return view('technical.sellers.index');
    }

    public function create()
    {
        $managers = $this->managersService->lists();

        $seller = [
            'quotation_modifier' => 0, 'profit_percentage' => 0,
            'balance' => 1000, 'daily_limit' => 100, 'sales_goal' => 0, 'limit' => 1000,
            'max_prize' => 12000, 'max_prize_multiplier' => 500,
            'one_tip_quotation_min' => 1.6, 'two_tip_quotation_min' => 1.10,
            'three_tip_quotation_min' => 1.01, 'tips_min' => 1, 'tips_max' => 25,
            'commission1' => 6, 'value_min1' => 2, 'value_max1' => 100,
            'commission2' => 10, 'value_min2' => 2, 'value_max2' => 300,
            'commission3' => 12, 'value_min3' => 2, 'value_max3' => 300,
            'commission6' => 12, 'value_min6' => 2, 'value_max6' => 300,
            'commission11' => 12, 'value_min11' => 2, 'value_max11' => 300,
            'commission16' => 12, 'value_min16' => 2, 'value_max16' => 300,
            'comissao_ao_vivo' => 8,
        ];

        return view('technical.sellers.create', compact('managers', 'seller'));
    }

    public function store(SellersCreateRequest $request)
    {
        try {
            $data = $request->except(['_token', 'password_confirmation']);

            $manager = $this->managersService->find($data['user_id']);
            $data['company_id'] = $manager->company_id;

            $data['balance'] = $data['balance'] > $data['limit'] ? $data['limit'] : $data['balance'];

            $this->service->createSeller($data);

            return redirect()->route('technical.sellers.index')->with('success', 'Cambista cadastrado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->route('technical.sellers.index')->with('error', "Erro ao cadastrar. {$e->getMessage()}");
        }
    }

    public function edit($id)
    {
        try {
            $managers = $this->managersService->lists();

            $seller = $this->service->find($id);

            return view('technical.sellers.edit', compact('seller', 'managers'));

        } catch (\Throwable $e) {
            return redirect()->route('technical.sellers.index')->with('error', "Erro ao editar. {$e->getMessage()}");
        }
    }

    public function update(SellersUpdateRequest $request, $id)
    {
        try {
            $data = $request->except(['_token', 'password_confirmation']);

            $manager = $this->managersService->find($data['user_id']);
            $data['company_id'] = $manager->company_id;

            $data['balance'] = $data['balance'] > $data['limit'] ? $data['limit'] : $data['balance'];

            $this->service->updateSeller($data, $id);

            return redirect()->route('technical.sellers.index')->with('success', 'Cambista atualizado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->route('technical.sellers.index')->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function all()
    {
        try {
            $sellers = $this->service
                ->setOnlyActives(false)
                ->getAll();

            return response()->json([
                'sellers' => $sellers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar cambistas',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function changeStatus($id)
    {
        try {
            $seller = $this->service->find($id);

            $seller->active = !$seller->active;
            $seller->save();

            $msg = $seller->active ? 'Cambista ativado com sucesso!' : 'Cambista desativado com sucesso!';

            return redirect()->route('technical.sellers.index')->with('success', $msg);

        } catch (\Throwable $e) {
            return redirect()->route('technical.sellers.index')->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function pluck(Request $request)
    {
        try {
            $companyId = $request->has('companyId')
                ? $request->get('companyId') : null;

            $managerId = $request->has('managerId')
                ? $request->get('managerId') : null;

            $sellers = $this->service
                ->setCompaniesIDs($companyId)
                ->setManagerId($managerId)
                ->setSelectFields(['id AS value', 'name AS label'])
                ->getAll();

            return response()->json([
                'sellers' => $sellers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar gerentes',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
