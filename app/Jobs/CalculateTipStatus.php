<?php

namespace Bets\Jobs;

use Bets\Models\Result;
use Bets\Models\Tip;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CalculateTipStatus implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;
    /**
     * @var Tip
     */
    private $tip;
    /**
     * @var Result
     */
    private $result;

    /**
     * Create a new job instance.
     *
     * @param Tip $tip
     * @param Result $result
     */
    public function __construct(Tip $tip, Result $result)
    {
        $this->tip = $tip;
        $this->result = $result;
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        $matchWinner = $this->calculateMatchWinner();
        $firstHalfWinner = $this->calculateFirstHalfResult();
        $secondHalfWinner = $this->calculateSecondHalfTimeWinner();
        $totalGoals = $this->calculateTotalGoals();

        switch ($this->tip->bet_slug) {
            case 'full_time_result':
                $this->fullTimeResult($matchWinner);
                break;
            case 'goals_over_under':
                $this->goalsOverUnder($totalGoals);
                break;
            case 'exact_total_goals':
                $this->exactTotalGoals($totalGoals);
                break;
            case 'both_teams_to_score':
                $this->bothTeamsToScore();
                break;
            case 'double_chance':
                $this->doubleChance($matchWinner);
                break;
            case 'result_both_teams_to_score':
                $this->resultBothTeamsToScore($matchWinner);
                break;
            case 'correct_score':
                $this->correctScore();
                break;
            case 'teams_to_score':
                $this->teamsToScore();
                break;
            case 'result_total_goals':
                $this->resultTotalGoals($matchWinner, $totalGoals);
                break;
            case 'goals_odd_even':
                $this->goalsOddEven($totalGoals);
                break;
            case 'half_time_full_time':
                $this->halfTimeFullTime($firstHalfWinner, $matchWinner);
                break;
            case 'half_with_most_goals':
                $this->halfWithMostGoals();
                break;
            case 'half_time_result':
                $this->halfTimeResult($firstHalfWinner);
                break;
            case 'half_time_double_chance':
                $this->halfTimeDoubleChance($firstHalfWinner);
                break;
            case 'half_time_correct_score':
                $this->halfTimeCorrectScore();
                break;
            case 'both_teams_to_score_in_1st_half':
                $this->bothTeamsToScoreIn1stHalf();
                break;
            case 'both_teams_to_score_in_2nd_half':
                $this->bothTeamsToScoreIn2ndHalf();
                break;
            case '1st_half_goals_odd_even':
                $this->firstHalfGoalsOddEven();
                break;
            case '2nd_half_result':
                $this->secondHalfResult($secondHalfWinner);
                break;
            case 'first_half_goals':
                $this->firstHalfGoals();
                break;
            case '2nd_half_goals':
                $this->secondHalfGoals();
                break;
            case '2nd_half_goals_odd_even':
                $this->secondHalfGoalsOddEven();
                break;
            case 'to_win_match':
                $this->toWinMatch($matchWinner);
                break;
            case 'handicap':
                $this->handicap();
                break;
            case 'home-team-odd-even-goals':
                $this->goalsOddEven($this->result->home_final);
                break;
            case 'away-team-odd-even-goals':
                $this->goalsOddEven($this->result->away_final);
                break;
            case 'teams-to-score':
                $this->timesQueMarcam();
                break;
            case 'home-team-exact-goals':
                $this->numeroExatoDeGols($this->result->home_final, 3);
                break;
            case 'away-team-exact-goals':
                $this->numeroExatoDeGols($this->result->away_final, 3);
                break;
            case 'exact-1st-half-goals':
                $this->numeroExatoDeGols($this->result->home_1st + $this->result->away_1st, 5);
                break;
            case 'exact-2nd-half-goals':
                $this->numeroExatoDeGols($this->result->home_2nd + $this->result->away_2nd, 5);
                break;
            case 'resultado-total-gols':
                $this->vencedorTotalGols($matchWinner, $totalGoals);
                break;
            case '1o-tempo-resultado-ambas-marcam':
                $this->vencedorAmbasMarcamPrimeiroTempo($firstHalfWinner);
                break;
            case '1o-tempo-resultado-total-gols':
                $this->vencedorTotalGolsPrimeiroTempo($firstHalfWinner);
                break;
            case 'total-gols-ambas-marcam':
                $this->totalGolsAmbasMarcam();
                break;
            case 'casa-tempo-mais-produtivo':
                $this->casaTempoMaisProdutivo();
                break;
            case 'fora-tempo-mais-produtivo':
                $this->foraTempoMaisProdutivo();
                break;
            case 'ambos-times-marcam-1o-tempo-2o-tempo':
                $this->ambasMarcamPrimeiroSegundoTempo();
                break;
            case 'empate-anula-aposta':
                $this->empateAnulaAposta($matchWinner);
                break;
        }

        if ($this->tip->bet->status !== 'canceled') {
            return $this->dispatch((new CalculateBetStatus($this->tip->bet))->onQueue('betStatus'));
        } else {
            return false;
        }
    }

    private function calculateMatchWinner()
    {
        $homeScore = $this->result->home_final;
        $awayScore = $this->result->away_final;

        if ((int)$homeScore > (int)$awayScore) {
            return '1';
        } elseif ((int)$homeScore < (int)$awayScore) {
            return '2';
        } else {
            return 'X';
        }
    }

    private function calculateTotalGoals()
    {
        return $this->result->home_final + $this->result->away_final;
    }

    private function calculateFirstHalfResult()
    {
        $homeScore = $this->result->home_1st;
        $awayScore = $this->result->away_1st;

        if ($homeScore > $awayScore) {
            return '1';
        } elseif ($homeScore < $awayScore) {
            return '2';
        } else {
            return 'X';
        }
    }

    private function calculateSecondHalfTimeWinner()
    {
        $homeScore = $this->result->home_2nd;
        $awayScore = $this->result->away_2nd;

        if ($homeScore > $awayScore) {
            return '1';
        } elseif ($homeScore < $awayScore) {
            return '2';
        } else {
            return 'X';
        }
    }

    private function loseTip()
    {
        $this->tip->status = 'lose';

        if ($this->tip->bet->status !== 'canceled') {
            $this->tip->bet->status = 'lose';
            $this->tip->bet->save();
        }

        return $this->tip->save();
    }

    private function winTip()
    {
        $this->tip->status = 'win';
        return $this->tip->save();
    }

    private function fullTimeResult($matchWinner)
    {
        if ($this->tip->choice_slug == $matchWinner) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function goalsOverUnder($totalGoals)
    {
        $choice = explode(' ', $this->tip->choice_slug);

        if (strcasecmp($choice[0], 'over') === 0) {
            if ($totalGoals >= $choice[1]) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        } else {
            if ($totalGoals <= $choice[1]) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        }
    }

    private function exactTotalGoals($totalGoals)
    {
        if ($this->tip->choice_slug == $totalGoals) {
            return $this->winTip();
        } elseif ($this->tip->choice_slug == '7+' && $totalGoals >= 7) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function bothTeamsToScore()
    {
        $homeScore = (int)$this->result->home_final > 0 ? true : false;
        $awayScore = (int)$this->result->away_final > 0 ? true : false;

        if ($homeScore && $awayScore && $this->tip->choice_slug === 'yes') {
            return $this->winTip();
        } elseif (((!$homeScore && $awayScore) || ($homeScore && !$awayScore)) && $this->tip->choice_slug === 'no') {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function doubleChance($matchWinner)
    {
        $choice = str_split($this->tip->choice_slug);

        if (in_array($matchWinner, $choice)) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function resultBothTeamsToScore($matchWinner)
    {
        $choice = explode('&', $this->tip->choice_slug);
        $bothTeamScore = $this->result->home_final > 0 && $this->result->away_final > 0 ? 'yes' : 'no';

        if (trim($choice[0]) == $matchWinner && $bothTeamScore == trim($choice[1])) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function correctScore()
    {
        $correctScore = $this->result->home_final . "-" . $this->result->away_final;

        if ($correctScore === $this->tip->choice_slug) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function teamsToScore()
    {
        $homeScore = $this->result->home_final;
        $awayScore = $this->result->away_final;

        if ($this->tip->choice_slug == 1 && $homeScore > 0 && $awayScore === 0) {
            return $this->winTip();
        } elseif ($this->tip->choice_slug == 2 && $homeScore === 0 && $awayScore > 0) {
            return $this->winTip();
        } elseif ($this->tip->choice_slug == 0 && $homeScore === 0 && $awayScore === 0) {
            return $this->winTip();
        } elseif ($this->tip->choice_slug == 'X' && $homeScore === $awayScore) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function resultTotalGoals($matchWinner, $totalGoals)
    {
        // falta terminar de implementar
        $choice = explode('&', $this->tip->choice_slug);

        if (strcasecmp(trim($choice[0]), $matchWinner) === 0) {
            $choice = explode(' ', $this->tip->choice_slug);

            if (strcasecmp($choice[0], 'over') === 0) {
                if ($totalGoals >= $choice[1]) {
                    return $this->winTip();
                } else {
                    return $this->loseTip();
                }
            } else {
                if ($totalGoals <= $choice[1]) {
                    return $this->winTip();
                } else {
                    return $this->loseTip();
                }
            }
        } else {
            return $this->loseTip();
        }
    }

    private function goalsOddEven($totalGoals)
    {
        $result = 'odd';

        if (($totalGoals % 2) === 0) {
            $result = 'even';
        }

        if ($this->tip->choice_slug === $result) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function halfTimeFullTime($firstHalfWinner, $matchWinner)
    {
        $choices = explode('-', $this->tip->choice_slug);

        if (trim($choices[0]) === $firstHalfWinner && trim($choices[1]) === $matchWinner) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function halfWithMostGoals()
    {
        $firstScore = $this->result->home_1st + $this->result->away_1st;
        $secondScore = $this->result->home_2nd + $this->result->away_2nd;

        if ($firstScore > $secondScore) {
            $halfWithMostGoals = '1';
        } elseif ($firstScore < $secondScore) {
            $halfWithMostGoals = '2';
        } else {
            $halfWithMostGoals = 'X';
        }

        if ($halfWithMostGoals === $this->tip->choice_slug) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function halfTimeResult($firstHalfWinner)
    {
        if ($this->tip->choice_slug == $firstHalfWinner) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function halfTimeDoubleChance($firstHalfWinner)
    {
        $choice = str_split($this->tip->choice_slug);

        if (in_array($firstHalfWinner, $choice)) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function halfTimeCorrectScore()
    {
        $correctScore = $this->result->home_1st . "-" . $this->result->away_1st;

        if ($correctScore === $this->tip->choice_slug) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function bothTeamsToScoreIn1stHalf()
    {
        $homeScore = (int)$this->result->home_1st > 0 ? true : false;
        $awayScore = (int)$this->result->away_1st > 0 ? true : false;

        if ($homeScore && $awayScore && $this->tip->choice_slug === 'yes') {
            return $this->winTip();
        } elseif (((!$homeScore && $awayScore) || ($homeScore && !$awayScore)) && $this->tip->choice_slug === 'no') {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function bothTeamsToScoreIn2ndHalf()
    {
        $homeScore = (int)$this->result->home_2nd > 0 ? true : false;
        $awayScore = (int)$this->result->away_2nd > 0 ? true : false;

        if ($homeScore && $awayScore && $this->tip->choice_slug === 'yes') {
            return $this->winTip();
        } elseif (((!$homeScore && $awayScore) || ($homeScore && !$awayScore)) && $this->tip->choice_slug === 'no') {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function firstHalfGoalsOddEven()
    {
        $totalGoals = $this->result->home_1st + $this->result->away_1st;
        $result = 'odd';

        if (($totalGoals % 2) === 0) {
            $result = 'even';
        }

        if ($this->tip->choice_slug === $result) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function secondHalfResult($secondHalfWinner)
    {
        if ($this->tip->choice_slug == $secondHalfWinner) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function firstHalfGoals()
    {
        $totalGoals = $this->result->home_1st + $this->result->away_1st;
        $choice = explode(' ', $this->tip->choice_slug);

        if (strcasecmp($choice[0], 'over') === 0) {
            if ($totalGoals >= $choice[1]) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        } else {
            if ($totalGoals <= $choice[1]) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        }
    }

    private function secondHalfGoals()
    {
        $totalGoals = $this->result->home_2nd + $this->result->away_2nd;
        $choice = explode(' ', $this->tip->choice_slug);

        if (strcasecmp($choice[0], 'over') === 0) {
            if ($totalGoals >= $choice[1]) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        } else {
            if ($totalGoals <= $choice[1]) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        }
    }

    private function secondHalfGoalsOddEven()
    {
        $totalGoals = $this->result->home_2nd + $this->result->away_2nd;
        $result = 'odd';

        if (($totalGoals % 2) === 0) {
            $result = 'even';
        }

        if ($this->tip->choice_slug === $result) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function toWinMatch($matchWinner)
    {
        if ($this->tip->choice_slug == $matchWinner) {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function handicap()
    {
        $homeScore = $this->result->home_final;
        $awayScore = $this->result->away_final;

        $choices = explode(' ', $this->tip->choice_slug);

        $homeHandicap = strcasecmp($choices[0], 'home') === 0
            ? (float)$choices[1] : 0;
        $awayHandicap = strcasecmp($choices[0], 'away') === 0
            ? (float)$choices[1] : 0;

        $homeFinalScore = $homeScore + $homeHandicap;
        $awayFinalScore = $awayScore + $awayHandicap;

        if ($choices[1] === '-1.5') {
            if ((float)$homeFinalScore > (float)$awayFinalScore) {
                $winner = 'home';
            } elseif ((float)$homeFinalScore < (float)$awayFinalScore) {
                $winner = 'away';
            } else {
                $winner = 'X';
            }

            if (strcasecmp($choices[0], $winner) === 0) {
                return $this->winTip();
            } else {
                return $this->loseTip();
            }
        }

        return false;
    }

    private function timesQueMarcam()
    {
        $casaMarca = $this->result->home_final > 0;
        $foraMarca = $this->result->away_final > 0;

        if ($casaMarca && $foraMarca && $this->tip->choice_slug === 'both') {
            return $this->winTip();
        } elseif (!$casaMarca && !$foraMarca && $this->tip->choice_slug === 'no-goal') {
            return $this->winTip();
        } elseif ($casaMarca && !$foraMarca && $this->tip->choice_slug === '1') {
            return $this->winTip();
        } elseif (!$casaMarca && $foraMarca && $this->tip->choice_slug === '2') {
            return $this->winTip();
        } else {
            return $this->loseTip();
        }
    }

    private function numeroExatoDeGols($totalGols, $numeroMaximo)
    {
        if ($this->tip->choice_slug == $totalGols) {
            return $this->winTip();
        } elseif ($this->tip->choice_slug == "{$numeroMaximo}+" && $totalGols >= $numeroMaximo) {
            return $this->winTip();
        }
        return $this->loseTip();
    }

    private function vencedorTotalGols($vencedor, $totalGoals)
    {
        $opcao = explode(' - ', $this->tip->choice_slug);

        if (trim($opcao[0]) === $vencedor) {
            $totalGols = explode(' ', $opcao[1]);

            if ($totalGols[0] === 'over') {
                if ($totalGoals >= ceil($totalGols[1])) {
                    return $this->winTip();
                }
            } else {
                if ($totalGoals <= floor($totalGols[1])) {
                    return $this->winTip();
                }
            }

            return $this->loseTip();
        }
        return $this->loseTip();
    }

    private function vencedorAmbasMarcamPrimeiroTempo($vencedor)
    {
        $opcao = explode(' - ', $this->tip->choice_slug);

        $ambasMarcam = $this->result->home_1st > 0 && $this->result->away_1st > 0;

        if (trim($opcao[0]) === $vencedor) {
            $palpite = trim($opcao[1]);
            if ($ambasMarcam && $palpite === 'sim') {
                return $this->winTip();
            } elseif (!$ambasMarcam && $palpite === 'nao') {
                return $this->winTip();
            }

            return $this->loseTip();
        }

        return $this->loseTip();
    }

    private function vencedorTotalGolsPrimeiroTempo($vencedor)
    {
        $opcao = explode(' - ', $this->tip->choice_slug);

        $totalGoals = (int)$this->result->home_1st + (int)$this->result->away_1st;

        if (trim($opcao[0]) === $vencedor) {
            $totalGols = explode(' ', $opcao[1]);

            if ($totalGols[0] === 'over') {
                if ($totalGoals >= ceil($totalGols[1])) {
                    return $this->winTip();
                }
            } else {
                if ($totalGoals <= floor($totalGols[1])) {
                    return $this->winTip();
                }
            }

            return $this->loseTip();
        }

        return $this->loseTip();
    }

    private function totalGolsAmbasMarcam()
    {
        $placarFinal = $this->result->home_final + $this->result->away_final;
        $ambasMarcam = $this->result->home_final > 0 && $this->result->away_final > 0
            ? 'sim' : 'nao';

        $opcao = explode(' - ', $this->tip->choice_slug);
        $totalGols = explode(' ', trim($opcao[0]));

        if ($ambasMarcam !== trim($opcao[1])) {
            return $this->loseTip();
        }

        if ($totalGols[0] === 'over') {
            if ($placarFinal >= ceil($totalGols[1])) {
                return $this->winTip();
            }
        } else {
            if ($placarFinal <= floor($totalGols[1])) {
                return $this->winTip();
            }
            return $this->loseTip();
        }

        return $this->loseTip();
    }

    private function casaTempoMaisProdutivo()
    {
        $placar1tempo = $this->result->home_1st;
        $placar2tempo = $this->result->home_2nd;

        if ($placar1tempo > $placar2tempo) {
            $tempoMaisProdutivo = '1';
        } elseif ($placar1tempo < $placar2tempo) {
            $tempoMaisProdutivo = '2';
        } else {
            $tempoMaisProdutivo = 'X';
        }

        if ($tempoMaisProdutivo === $this->tip->choice_slug) {
            return $this->winTip();
        }

        return $this->loseTip();
    }

    private function foraTempoMaisProdutivo()
    {
        $placar1tempo = $this->result->away_1st;
        $placar2tempo = $this->result->away_2nd;

        if ($placar1tempo > $placar2tempo) {
            $tempoMaisProdutivo = '1';
        } elseif ($placar1tempo < $placar2tempo) {
            $tempoMaisProdutivo = '2';
        } else {
            $tempoMaisProdutivo = 'X';
        }

        if ($tempoMaisProdutivo === $this->tip->choice_slug) {
            return $this->winTip();
        }

        return $this->loseTip();
    }

    private function ambasMarcamPrimeiroSegundoTempo()
    {
        $ambasMarcam1tempo = $this->result->home_1st > 0 && $this->result->away_1st > 0
            ? 'sim' : 'nao';

        $ambasMarcam2tempo = $this->result->home_2nd > 0 && $this->result->away_2nd > 0
            ? 'sim' : 'nao';

        $palpite = "{$ambasMarcam1tempo} - {$ambasMarcam2tempo}";

        if ($palpite === $this->tip->choice_slug) {
            return $this->winTip();
        }

        return $this->loseTip();
    }

    private function empateAnulaAposta($vencedor)
    {
        if ($vencedor != 'X') {
            $this->fullTimeResult($vencedor);

            return;
        }

        if (is_null($this->tip->bet->seller_id)) {
            $this->tip->status = 'canceled';

            $this->tip->save();

            return;
        }

        $service = app()->make('Bets\Services\TipsService');

        $service->cancel($this->tip->id);
    }
}
