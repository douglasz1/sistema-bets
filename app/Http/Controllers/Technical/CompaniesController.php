<?php

namespace Bets\Http\Controllers\Technical;

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

        return view('technical.companies.index', compact('companies'));
    }

    public function quotationsUp()
    {
        try {

            $this->companiesService->updateQuotations(true);

            return redirect()->route('technical.companies.index')
                ->with('success', 'Empresas alteradas com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->route('technical.companies.index')
                ->with('error', "Erro ao atualizar. {$e->getMessage()}");
        }
    }

    public function quotationsDown()
    {
        try {

            $this->companiesService->updateQuotations(false);

            return redirect()->route('technical.companies.index')
                ->with('success', 'Empresas alteradas com sucesso!');

        } catch (\Throwable $e) {
            return redirect()->route('technical.companies.index')
                ->with('error', "Erro ao atualizar. {$e->getMessage()}");
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
}
