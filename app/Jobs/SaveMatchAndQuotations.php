<?php

namespace Bets\Jobs;

use Bets\Services\GetJsonService;
use Bets\Services\MatchesService;
use Bets\Services\QuotationsPopulateService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveMatchAndQuotations implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var array
     */
    private $data;

    /**
     * Create a new job instance.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @param GetJsonService $jsonService
     * @param MatchesService $matchesService
     * @param QuotationsPopulateService $quotationsService
     * @return bool
     * @throws \Exception
     */
    public function handle(GetJsonService $jsonService, MatchesService $matchesService, QuotationsPopulateService $quotationsService)
    {
        $match = $matchesService->updateOrCreate($this->data);

        if ($match->sport_slug === 'soccer') {
            $dadosEvento = $jsonService->partida($match->match_id);

            if (!empty($dadosEvento['data'])) {
                $quotationsService->createFromJson($dadosEvento['data']['quotations'], $match);
            }

            return false;
        }

        $eventData = $jsonService->getEvent((int)$match->match_id);

        if ($eventData['result'] === 'success') {
            $quotationsService->createFromJson($eventData['quotations'], $match);
        }
        return false;
    }
}
