<?php

namespace Bets\Jobs\NovaApi;

use Bets\Services\MatchesService;
use Bets\Services\NovaApi\ImportarEventosService;
use Bets\Services\QuotationsPopulateService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SalvarEvento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $dadosEvento;

    /**
     * Create a new job instance.
     *
     * @param array $dadosEvento
     */
    public function __construct(array $dadosEvento)
    {
        $this->dadosEvento = $dadosEvento;
    }

    /**
     * Execute the job.
     *
     * @param ImportarEventosService $importarEventosService
     * @param MatchesService $matchesService
     * @param QuotationsPopulateService $quotationsService
     * @return void
     */
    public function handle(ImportarEventosService $importarEventosService, MatchesService $matchesService, QuotationsPopulateService $quotationsService)
    {
        try {
            $evento = $matchesService->updateOrCreate($this->dadosEvento);

            $dadosEvento = $importarEventosService->evento($evento->match_id);

            if (!empty($dadosEvento['data'])) {
                $quotationsService->createFromJson($dadosEvento['data']['quotations'], $evento);
            }

        } catch (\Throwable $exception) {
            \Log::error("Erro ao salvar evento: {$this->dadosEvento['match_id']}");
        }
    }
}
