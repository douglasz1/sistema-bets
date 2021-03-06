<?php

namespace Bets\Http\Controllers;

use Bets\Services\CompaniesService;
use Bets\Services\SportsService;
use Bets\Services\UsersService;
use Illuminate\Http\Request;

class ConfigsController extends Controller
{

    /**
     * @var CompaniesService
     */
    private $companiesService;
    /**
     * @var SportsService
     */
    private $sportsService;

    public function __construct(CompaniesService $companiesService, SportsService $sportsService)
    {
        $this->companiesService = $companiesService;
        $this->sportsService = $sportsService;
    }

    public function simulator()
    {
        $sports = $this->sportsService->actives()->all();
        $aoVivoDisponivel = false;

        $alterarCotacoes = 0;

        if (auth()->check()) {
            $user = auth()->user();

            if (!is_null($user->company_id)) {
                $alterarCotacoes = $user->quotationModifier() + ($user->company->quotation_modifier / 100);
            }

            $aoVivoDisponivel = $user->ao_vivo;
            $maxPrize = $user->max_prize;
            $maxPrizeMultiplier = $user->max_prize_multiplier;
        } else {
            $company = $this->companiesService->first();

            $maxPrize = $company->max_prize;
            $maxPrizeMultiplier = $company->max_prize_multiplier;
        }

        return response()->json([
            'alterarCotacoes' => $alterarCotacoes,
            'ao_vivo' => $aoVivoDisponivel,
            'maxPrize' => $maxPrize,
            'maxPrizeMultiplier' => $maxPrizeMultiplier,
            'sports' => $sports->makeHidden(['created_at', 'updated_at', 'active', 'api_id']),
        ]);
    }
}
