<?php

namespace Bets\Http\Controllers\Admin;

use Bets\Services\Admin\CompaniesService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class CompaniesController extends Controller
{
    /**
     * @var CompaniesService
     */
    private $companiesService;

    public function __construct(CompaniesService $companiesService)
    {
        $this->companiesService = $companiesService;
    }

    public function index()
    {
        $companies = $this->companiesService->getAll();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $company = [
            'name' => 'Bets', 'print_name',
            'quotation_modifier' => 0,
            'max_prize' => 12000,
            'max_prize_multiplier' => 500,
            'bets_limit' => 0
        ];

        return view('admin.companies.create', compact('company'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->except('_token');

            $this->companiesService->create($data);

            return redirect()->route('admin.companies.index')->with('success', 'Empresa cadastrada com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->route('admin.companies.index')->with('error', "Erro ao cadastrar. {$e->getMessage()}");
        }
    }

    public function edit($id)
    {
        $company = $this->companiesService->find($id);

        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token');

            $this->companiesService->update($data, $id);

            return redirect()->route('admin.companies.index')->with('success', 'Empresa alterada com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->route('admin.companies.index')->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function supervisors($id)
    {
        return view('admin.companies.supervisors', compact('id'));
    }

    public function getSupervisors($id)
    {
        try {
            $company = $this->companiesService->find($id);

            return response()->json([
                'company' => $company,
                'supervisors' => $company->supervisors
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar supervisores',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function saveSupervisors(Request $request, $id)
    {
        try {
            $data = $request->get('supervisors');

            $supervisors = [];

            foreach ($data as $item) {
                $supervisors[$item['id']] = [
                    'percentage' => $item['percentage']
                ];
            }

            $this->companiesService->attach($supervisors, $id);

            return response()->json([
                'message' => 'Dados salvos com sucesso'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao salvar supervisores',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function pluck()
    {
        try {
            $companies = $this->companiesService
                ->setSelectFields(['id AS value', 'name AS label'])
                ->getAll();

            return response()->json([
                'companies' => $companies
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao listar empresas',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function rulesUpdate(Request $request, $id)
    {
        try {
            $data = $request->only('rules');

            $this->companiesService->update($data, $id);

            return redirect()->back()->with('success', 'Regras alteradas com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function quotationsUp()
    {
        try {

            $this->companiesService->updateQuotations(true);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Empresas alteradas com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function quotationsDown()
    {
        try {

            $this->companiesService->updateQuotations(false);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Empresas alteradas com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }
}
