<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Services\Supervisor\ManagersService;
use Bets\Services\Supervisor\SellersService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;

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
        return view('supervisor.sellers.index');
    }

    public function create()
    {
        $companiesId = Arr::pluck(auth()->user()->companies->toArray(), 'id');

        $managers = $this->managersService
            ->setCompaniesId($companiesId)
            ->lists();

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

        return view('supervisor.sellers.create', compact('managers', 'seller'));
    }

    public function store(Request $request)
    {
        $validacao = validator()->make($request->all(), [
            'name' => 'required|min:3',
            'username' => 'required|unique:users|min:3',
            'password' => 'required|confirmed|min:3',
        ], [
            'required' => 'Este campo é obrigatório!',
            'username.min' => 'O nome de usuário deve conter, no mínimo, 3 caracteres.',
            'username.unique' => 'Este nome de usuário já está em uso.',
        ]);

        try {
            $data = $request->except(['_token', 'password_confirmation']);

            $validacao->validate();

            $manager = $this->managersService->find($data['user_id']);
            $data['company_id'] = $manager->company_id;

            $operador = $this->service->createSeller($data);

            activity('usuario')
                ->performedOn($operador)
                ->causedBy(auth()->user())
                ->withProperties([
                    'attributes' => $request->except(['_token', 'password_confirmation', 'password']),
                ])
                ->log('created');

            return redirect()->route('supervisor.sellers.index')->with('success', 'Cambista cadastrado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validacao)
                ->with('error', "Erro ao cadastrar.");
        }
    }

    public function edit($id)
    {
        $companiesIDs = Arr::pluck(auth()->user()->companies->toArray(), 'id');

        $seller = $this->service->find($id);

        if (!$this->isMyUser($seller->company_id, $companiesIDs)) {
            return redirect()->route('supervisor.sellers.index')->with('error', 'Você não pode acessar esse vendedor');
        }

        $managers = $this->managersService
            ->setCompaniesId($companiesIDs)
            ->lists();

        return view('supervisor.sellers.edit', compact('seller', 'managers'));
    }

    public function update(Request $request, $id)
    {
        $validacao = validator()->make($request->all(), [
            'name' => 'required|min:3',
            'username' => [
                'required',
                Rule::unique('users')->ignore($id)
            ],
            'password' => 'confirmed|min:3',
        ], [
            'required' => 'Este campo é obrigatório!',
            'username.min' => 'O nome de usuário deve conter, no mínimo, 3 caracteres.',
            'username.unique' => 'Este nome de usuário já está em uso.',
        ]);

        try {
            $companiesIDs = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            $validacao->validate();

            $seller = $this->service->find($id);

            if (!$this->isMyUser($seller->company_id, $companiesIDs)) {
                return redirect()->route('supervisor.sellers.index')->with('error', 'Você não pode acessar esse cambista');
            }

            $data = $request->except(['_token', 'password_confirmation']);

            $manager = $this->managersService->find($data['user_id']);
            $data['company_id'] = $manager->company_id;

            $this->service->updateSeller($data, $id);

            return redirect()->route('supervisor.sellers.index')->with('success', 'Cambista atualizado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validacao)
                ->with('error', "Erro ao atualizar.");
        }
    }

    public function all()
    {
        try {
            $companiesIDs = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            $sellers = $this->service
                ->setCompaniesIDs($companiesIDs)
                ->setOnlyActives(false)
                ->getAll();

            return response()->json([
                'sellers' => $sellers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao carregador dados dos cambistas',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function changeStatus($id)
    {
        try {
            $companiesIDs = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            $seller = $this->service->find($id);

            if (!$this->isMyUser($seller->company_id, $companiesIDs)) {
                return redirect()->route('supervisor.sellers.index')->with('error', 'Você não tem permissão para editar esse cambista');
            }

            $seller->active = !$seller->active;
            $seller->save();

            $msg = $seller->active ? 'Cambista ativado com sucesso!' : 'Cambista desativado com sucesso!';

            return redirect()->route('supervisor.sellers.index')->with('success', $msg);

        } catch (\Throwable $e) {
            return redirect()->route('supervisor.sellers.index')->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function pluck(Request $request)
    {
        try {
            $companyId = $request->filled('companyId')
                ? $request->get('companyId') : null;

            if (is_null($companyId)) {
                $companyId = Arr::pluck(auth()->user()->companies->toArray(), 'id');
            }

            $managerId = $request->filled('managerId')
                ? $request->get('managerId') : null;

            $sellers = $this->service
                ->setCompaniesIDs($companyId)
                ->setSelectFields(['id AS value', 'name AS label'])
                ->setManagerId($managerId)
                ->getAll();

            return response()->json([
                'sellers' => $sellers
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao carregador dados dos cambistas',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    private function isMyUser($userId, $companiesIDs)
    {
        return in_array($userId, $companiesIDs);
    }
}
