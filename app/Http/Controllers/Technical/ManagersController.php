<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Http\Requests\Admin\ManagersCreateRequest;
use Bets\Http\Requests\Admin\ManagersUpdateRequest;
use Bets\Services\Admin\ManagersService;
use Bets\Services\Admin\CompaniesService;
use Illuminate\Http\Request;

class ManagersController extends Controller
{
    /**
     * @var CompaniesService
     */
    private $companiesService;
    /**
     * @var ManagersService
     */
    private $service;

    public function __construct(CompaniesService $companiesService, ManagersService $service)
    {
        $this->companiesService = $companiesService;
        $this->service = $service;
    }

    public function index()
    {
        return view('technical.managers.index');
    }

    public function create()
    {
        $manager = [
            'quotation_modifier' => 0, 'profit_percentage' => 0,
            'manager_commission' => 0,
            'balance' => 1000, 'daily_limit' => 100, 'limit' => 1000,
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

        $companies = $this->companiesService->lists();

        return view('technical.managers.create', compact('manager', 'companies'));
    }

    public function store(ManagersCreateRequest $request)
    {
        try {
            $data = $request->except(['_token', 'password_confirmation']);

            $data['balance'] = $data['balance'] > $data['limit'] ? $data['limit'] : $data['balance'];

            $this->service->createManager($data);

            return redirect()->route('technical.managers.index')->with('success', 'Gerente cadastrado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->route('technical.managers.index')->with('error', "Erro ao cadastrar. {$e->getMessage()}");
        }
    }

    public function edit($id)
    {
        try {
            $manager = $this->service->find($id);

            $companies = $this->companiesService->lists();

            return view('technical.managers.edit', compact('manager', 'companies'));

        } catch (\Throwable $e) {
            return redirect()->route('technical.managers.index')->with('error', "Erro ao editar. {$e->getMessage()}");
        }
    }

    public function update(ManagersUpdateRequest $request, $id)
    {
        try {
            $data = $request->except(['_token', 'password_confirmation']);

            $data['balance'] = $data['balance'] > $data['limit'] ? $data['limit'] : $data['balance'];

            $this->service->updateManager($data, $id);

            return redirect()->route('technical.managers.index')->with('success', 'Gerente atualizado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->route('technical.managers.index')->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function all()
    {
        try {
            $managers = $this->service
                ->setOnlyActives(false)
                ->getAll();

            return response()->json([
                'managers' => $managers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar gerentes',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function changeStatus($id)
    {
        try {
            $manager = $this->service->find($id);

            $manager->active = !$manager->active;
            $manager->save();

            $msg = $manager->active ? 'Gerente ativado com sucesso!' : 'Gerente desativado com sucesso!';

            return redirect()->route('technical.managers.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('technical.managers.index')->with('error', "Erro ao atualizar gerente. {$e->getMessage()}");
        }
    }

    public function pluck(Request $request)
    {
        try {
            $companyId = $request->has('companyId')
                ? $request->get('companyId') : null;

            $managers = $this->service
                ->setCompaniesIDs($companyId)
                ->setSelectFields(['id AS value', 'name AS label'])
                ->getAll();

            return response()->json([
                'managers' => $managers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar gerentes',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
