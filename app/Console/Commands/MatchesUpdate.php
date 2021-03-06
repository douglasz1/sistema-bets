<?php

namespace Bets\Console\Commands;

use Bets\Services\GetJsonService;
use Bets\Services\MatchesService;
use Bets\Services\SportsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MatchesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update matches and their odds';
    /**
     * @var GetJsonService
     */
    private $getJsonService;
    /**
     * @var MatchesService
     */
    private $matchesService;
    /**
     * @var SportsService
     */
    private $sportsService;

    /**
     * Create a new command instance.
     *
     * @param GetJsonService $getJsonService
     * @param SportsService $sportsService
     * @param MatchesService $matchesService
     */
    public function __construct(GetJsonService $getJsonService,
                                SportsService $sportsService,
                                MatchesService $matchesService)
    {
        parent::__construct();
        $this->getJsonService = $getJsonService;
        $this->matchesService = $matchesService;
        $this->sportsService = $sportsService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->info('Cleaning oldest matches...');
            $this->matchesService->cleanMatches();

            $this->info('Fetching active sports...');
            $sports = $this->sportsService->actives()->all();

            foreach ($sports as $sport) {
                $this->info("Events from {$sport->name}");

                if ($sport->slug === 'soccer') continue;

                $page = 1;
                do {
                    $this->info('Fetching upcoming events...');
                    $eventos = $this->getJsonService->getUpcoming($page, (int)$sport->api_id);

                    if ($eventos['result'] === 'success') {
                        $page++;
                        $this->info('Saving new events in the database');

                        foreach ($eventos['matches'] as $evento) {
                            $this->matchesService->createFromJson($evento, $sport);
                        }
                    }

                    $hasMorePages = (bool)$eventos['has_more_pages'];
                } while ($hasMorePages); // end of events loop

            } // end of sports loop

            $this->info('Update finished!');
        } catch (\Throwable $exception) {
            Log::error("Erro ao atualizar partidas");
        }
    }
}
