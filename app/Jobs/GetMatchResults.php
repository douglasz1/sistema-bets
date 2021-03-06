<?php

namespace Bets\Jobs;

use Bets\Models\Result;
use Bets\Services\GetJsonService;
use Bets\Services\ResultsService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GetMatchResults implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    /**
     * @var Result
     */
    private $result;

    /**
     * Create a new job instance.
     *
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    public function handle(ResultsService $resultsService, GetJsonService $jsonService)
    {
        $dadosEvento = $jsonService->partida($this->result->match_id);

        if ($dadosEvento['data']['status'] === 'finished') {
            $placar = $dadosEvento['data']['placares'];

            if (is_null($placar['casa_1t']) || is_null($placar['fora_1t'])) {
                return;
            }

            $eventScores = [
                'status' => 'finished',
                'home_1st' => $placar['casa_1t'],
                'away_1st' => $placar['fora_1t'],
                'home_2nd' => $placar['casa_2t'],
                'away_2nd' => $placar['fora_2t'],
                'home_final' => $placar['casa_final'],
                'away_final' => $placar['fora_final'],
            ];

            $extras = $dadosEvento['data']['extras'];

            if (!empty($extras)) {
                if (isset($extras['escanteios'])) {
                    CalcularEscanteios::dispatch($this->result, $extras['escanteios'])
                        ->onQueue('betStatus');
                }

                // if (!empty($extras['gols'])) {
                //     CalcularMarcadores::dispatch($this->result, $extras['gols'])
                //         ->onQueue('betStatus');
                // }
            }

            $resultsService->update($eventScores, $this->result->id);
        } elseif ($dadosEvento['data']['status'] === 'canceled') {
            $resultsService->update(['status' => 'canceled'], $this->result->id);
        }
    }
}
