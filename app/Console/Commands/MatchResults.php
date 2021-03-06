<?php

namespace Bets\Console\Commands;

use Bets\Jobs\GetMatchResults;
use Bets\Services\GetJsonService;
use Bets\Services\ResultsService;
use Illuminate\Console\Command;

class MatchResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get matches results';
    /**
     * @var GetJsonService
     */
    private $jsonService;
    /**
     * @var ResultsService
     */
    private $resultsService;

    /**
     * Create a new command instance.
     *
     * @param GetJsonService $jsonService
     * @param ResultsService $resultsService
     */
    public function __construct(GetJsonService $jsonService, ResultsService $resultsService)
    {
        parent::__construct();
        $this->jsonService = $jsonService;
        $this->resultsService = $resultsService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Get past results...');
        $results = $this->resultsService->getPastResults();

        $bar = $this->output->createProgressBar($results->count());

        $this->info('Creating jobs...');
        foreach ($results as $result) {
            dispatch((new GetMatchResults($result))->onQueue('matches'));
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('Finished...');
    }
}
