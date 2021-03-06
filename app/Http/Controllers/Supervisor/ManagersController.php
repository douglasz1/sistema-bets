<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Http\Requests\Supervisor\ManagersCreate;
use Bets\Http\Requests\Supervisor\ManagersUpdate;
use Bets\Services\Supervisor\ManagersService;
use Bets\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ManagersController extends Controller
{
    /**
     * @var ManagersService
     */
    private $service;

    public function __construct(ManagersService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('supervisor.managers.index');
    }

    public function create()
    {
        $companiesIDs = auth()->user()->companies->toArray();

        $companies = Arr::pluck($companiesIDs, 'name', 'id');

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

        return view('supervisor.managers.create', compact('manager', 'companies'));
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

            $gerente = $this->service->createManager($data);

            activity('usuario')
                ->performedOn($gerente)
                ->causedBy(auth()->user())
                ->withProperties([
                    'attributes' => $request->except(['_token', 'password_confirmation', 'password']),
                ])
                ->log('created');

            return redirect()->route('supervisor.managers.index')->with('success', 'Gerente cadastrado com sucesso');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validacao)
                ->with('error', "Erro ao cadastrar.");
        }
    }

    public function edit($id)
    {
        $companiesIDs = auth()->user()->companies->toArray();

        $companies = Arr::pluck($companiesIDs, 'name', 'id');

        $manager = $this->service->find($id);

        if (!$this->isMyUser($manager->company_id, Arr::pluck($companiesIDs, 'id'))) {
            return redirect()->route('supervisor.managers.index')->with('error', 'Você não tem permissão para editar esse gerente');
        }

        return view('supervisor.managers.edit', compact('manager', 'companies'));
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
            $validacao->validate();

            $companiesIDs = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            $manager = $this->service->find($id);

            if (!$this->isMyUser($manager->company_id, $companiesIDs)) {
                return redirect()->route('supervisor.managers.index')->with('error', 'Você não tem permissão para editar esse gerente');
            }

            $data = $request->except(['_token', 'password_confirmation']);

            $this->service->updateManager($data, $id);

            return redirect()->route('supervisor.managers.index')->with('success', 'Gerente atualizado com sucesso');

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
            $companiesId = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            $managers = $this->service
                ->setCompaniesId($companiesId)
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
            $companiesIDs = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            $manager = $this->service->find($id);

            if (!$this->isMyUser($manager->company_id, $companiesIDs)) {
                return redirect()->route('supervisor.managers.index')->with('error', 'Você não tem permissão para editar esse gerente');
            }

            $manager->active = !$manager->active;
            $manager->save();

            $msg = $manager->active ? 'Gerente ativado com sucesso!' : 'Gerente desativado com sucesso!';

            return redirect()->route('supervisor.managers.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('supervisor.managers.index')->with('error', "Erro ao atualizar gerente. {$e->getMessage()}");
        }
    }

    public function pluck(Request $request)
    {
        try {
            $companyId = $request->filled('companyId')
                ? $request->get('companyId') : null;

            $supervisorCompanies = Arr::pluck(auth()->user()->companies->toArray(), 'id');

            if (is_null($companyId) || !in_array($companyId, $supervisorCompanies)) {
                $companyId = $supervisorCompanies;
            }

            $managers = $this->service
                ->setCompaniesId($companyId)
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

    private function isMyUser($userId, $companiesIDs)
    {
        return in_array($userId, $companiesIDs);
    }
}
