<?php

namespace Bets\Http\Controllers\Supervisor;

use Bets\Services\Supervisor\CompaniesService;
use Illuminate\Http\Request;
use Bets\Http\Controllers\Controller;

class CompaniesController extends Controller
{
    /**
     * @var CompaniesService
     */
    private $service;

    public function __construct(CompaniesService $service)
    {
        $this->service = $service;
    }

    public function pluck()
    {
        try {
            $companies = $this->service
                ->setUserId(auth()->id())
                ->setSelectFields(['id AS value', 'name AS label'])
                ->getAll();

            return response()->json([
                'companies' => $companies,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Erro ao listar empresas'
            ]);
        }
    }
}
