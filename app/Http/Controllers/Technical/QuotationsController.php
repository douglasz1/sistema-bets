<?php

namespace Bets\Http\Controllers\Technical;

use Bets\Http\Controllers\Controller;
use Bets\Services\CompaniesService;
use Bets\Services\MatchesService;
use Bets\Services\QuotationsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuotationsController extends Controller
{
    /**
     * @var CompaniesService
     */
    private $companiesService;

    /**
     * @var QuotationsService
     */
    private $quotationsService;

    /**
     * @var MatchesService
     */
    private $matchesService;

    public function __construct(QuotationsService $quotationsService, MatchesService $matchesService, CompaniesService $companiesService)
    {
        $this->companiesService = $companiesService;
        $this->quotationsService = $quotationsService;
        $this->matchesService = $matchesService;
    }

    public function index()
    {
        return view('technical.quotations.index');
    }

    public function matches(Request $request)
    {
        $nowDate = Carbon::now()->toDateString();

        $filters = array(
            'start_date' => $request->has('start_date')
                ? $request->get('start_date')
                : $nowDate,
            'end_date' => $request->has('end_date')
                ? $request->get('end_date')
                : $nowDate,
            'league_id' => $request->has('league_id')
                ? $request->get('league_id')
                : null
        );

        $matches = $this->matchesService->getWithQuotations($filters);

        $user = auth()->user();
        try {
            $quotationModifier = $user->quotationModifier() + ($user->company->quotation_modifier / 100);
        } catch (\Throwable $e) {
            $quotationModifier = $user->quotationModifier() + ($this->companiesService->first()->quotation_modifier / 100);
        }

        $banca = \Bets\Models\Banca::query()->first();

        $matches->each(function ($item) use ($quotationModifier, $banca) {
            $item['human_date'] = $item->match_date->format('d/m H:i');
            $item['active'] = array_search($item->match_id, $banca->partidas_inativas_pre) === false;

            $quotations['home'] = $item->quotations->where('choice_slug', '1')->first()
                ?? ['value' => 0];
            $quotations['draw'] = $item->quotations->where('choice_slug', 'X')->first()
                ?? ['value' => 0];
            $quotations['away'] = $item->quotations->where('choice_slug', '2')->first()
                ?? ['value' => 0];
            $quotations['home_draw'] = $item->quotations->where('choice_slug', '1X')->first()
                ?? ['value' => 0];
            $quotations['away_draw'] = $item->quotations->where('choice_slug', 'X2')->first()
                ?? ['value' => 0];
            $quotations['home_away'] = $item->quotations->where('choice_slug', '12')->first()
                ?? ['value' => 0];
            $quotations['both_yes'] = $item->quotations->where('choice_slug', 'yes')->first()
                ?? ['value' => 0];
            $quotations['both_no'] = $item->quotations->where('choice_slug', 'no')->first()
                ?? ['value' => 0];

            unset($item->quotations);

            foreach ($quotations as $quotation) {
                $quotation['value'] = number_format($quotation['value'], 2);

                if ($quotation['value'] < 1.01) {
                    $quotation['value'] = 1.01;
                } elseif ($quotation['value'] > 100) {
                    $quotation['value'] = 100;
                }
            }

            $item['quotations'] = $quotations;
        });

        return response()->json([
            'matches' => $matches->toArray(),
            'quotationModifier' => $quotationModifier
        ]);
    }

    public function update(Request $request)
    {
        try {
            $match = $request->get('match');

            foreach ($match['quotations'] as $key => $quotation) {
                if ($quotation['value'] == 0) {
                    continue;
                }

                $data = [];

                if (!isset($quotation['id'])) {
                    if ($key === 'both_yes') {
                        $data = [
                            "bet_name" => "Ambas marcam",
                            "bet_slug" => "both_teams_to_score",
                            "choice_name" => "Ambas marcam",
                            "choice_slug" => "yes",
                        ];
                    } elseif ($key === 'both_no') {
                        $data = [
                            "bet_name" => "Ambas marcam",
                            "bet_slug" => "both_teams_to_score",
                            "choice_name" => "Apenas uma marca",
                            "choice_slug" => "no",
                        ];
                    } elseif ($key === 'home') {
                        $data = [
                            "bet_name" => "Vencedor",
                            "bet_slug" => "full_time_result",
                            "choice_name" => "Casa",
                            "choice_slug" => "1",
                        ];
                    } elseif ($key === 'draw') {
                        $data = [
                            "bet_name" => "Vencedor",
                            "bet_slug" => "full_time_result",
                            "choice_name" => "Empate",
                            "choice_slug" => "X",
                        ];
                    } elseif ($key === 'away') {
                        $data = [
                            "bet_name" => "Vencedor",
                            "bet_slug" => "full_time_result",
                            "choice_name" => "Fora",
                            "choice_slug" => "2",
                        ];
                    } elseif ($key === 'home_draw') {
                        $data = [
                            "bet_name" => "Dupla chance",
                            "bet_slug" => "double_chance",
                            "choice_name" => "Casa ou Empate",
                            "choice_slug" => "1X",
                        ];
                    } elseif ($key === 'away_draw') {
                        $data = [
                            "bet_name" => "Dupla chance",
                            "bet_slug" => "double_chance",
                            "choice_name" => "Fora ou Empate",
                            "choice_slug" => "X2",
                        ];
                    } elseif ($key === 'home_away') {
                        $data = [
                            "bet_name" => "Dupla chance",
                            "bet_slug" => "double_chance",
                            "choice_name" => "Casa ou Fora",
                            "choice_slug" => "12",
                        ];
                    }

                    $data["match_id"] = $match['id'];
                    $data["upgradable"] = false;
                    $data["value"] = $quotation['value'];

                    $this->quotationsService->firstOrCreate($data);

                } else {
                    $quotation['value'] = number_format($quotation['value'], 2);
                    if ((float)$quotation['value'] < 1.01) {
                        $quotation['value'] = 1.01;
                    } elseif ((float)$quotation['value'] > 100) {
                        $quotation['value'] = 100;
                    }

                    $data = ['value' => $quotation['value']];

                    $this->quotationsService->update($data, $quotation['id']);
                }
            }

            return response()->json(['match' => $match]);

        } catch (\Throwable $error) {
            return response()->json($error->getMessage(), 400);
        }
    }
}
