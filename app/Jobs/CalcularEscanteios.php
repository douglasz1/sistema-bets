<?php

namespace Bets\Jobs;

use Bets\Models\{Bet, Result, Tip};
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalcularEscanteios implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Result
     */
    private $result;
    private $escanteios;

    /**
     * Create a new job instance.
     *
     * @param Result $result
     * @param $escanteios
     */
    public function __construct(Result $result, $escanteios)
    {
        $this->result = $result;
        $this->escanteios = $escanteios;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $escanteios = $this->escanteios;

        $vencedores = ["exatos {$escanteios}"];
        $perdedores = ["mais {$escanteios}", "menos {$escanteios}"];

        for ($i = $escanteios - 1; $i >= 3; $i--) {
            $perdedores[] = "menos {$i}";
            $perdedores[] = "exatos {$i}";
            $vencedores[] = "mais {$i}";
        }

        for ($i = $escanteios + 1; $i <= 19; $i++) {
            $perdedores[] = "mais {$i}";
            $vencedores[] = "menos {$i}";
        }

        $vencedores = Tip::whereMatchId($this->result->match_id)
            ->where('bet_slug', 'escanteios')
            ->whereIn('choice_slug', $vencedores)
            ->select(['id', 'bet_id'])
            ->get();

        $perdedores = Tip::whereMatchId($this->result->match_id)
            ->where('bet_slug', 'escanteios')
            ->whereIn('choice_slug', $perdedores)
            ->select(['id', 'bet_id'])
            ->get();

        Tip::whereIn('id', $perdedores->pluck('id'))->update(['status' => 'lose']);
        Bet::whereIn('id', $perdedores->pluck('bet_id'))->update(['status' => 'lose']);

        Tip::whereIn('id', $vencedores->pluck('id'))->update(['status' => 'win']);

        $apostas = Bet::whereIn('id', $vencedores->pluck('bet_id'))
            ->where('status', 'pending')
            ->get();

        foreach ($apostas as $aposta) {
            dispatch((new CalculateBetStatus($aposta))->onQueue('betStatus'));
        }
    }
}
