<?php

namespace Bets\Console\Commands;

use Bets\Services\NovaApi\EventosService;
use Bets\Services\NovaApi\ImportarEventosService;
use Illuminate\Console\Command;

class PartidasAtualizar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partidas:atualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var EventosService
     */
    private $eventosService;

    /**
     * @var ImportarEventosService
     */
    private $importarEventosService;

    /**
     * Create a new command instance.
     *
     * @param EventosService $eventosService
     * @param ImportarEventosService $importarEventosService
     */
    public function __construct(EventosService $eventosService, ImportarEventosService $importarEventosService)
    {
        parent::__construct();
        $this->eventosService = $eventosService;
        $this->importarEventosService = $importarEventosService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $qtdJobs = \Queue::size('matches');

        if ($qtdJobs > 3000) {
            \Redis::connection()
                ->del(['queues:matches', 'queues:matches:notify']);
        }

        \Bets\Models\Result::whereNull('home_1st')
            ->whereDate('match_date', '>=', now()->toDateString())
            ->where('status', 'finished')
            ->where('have_tips', 1)
            ->update(['status' => 'pending']);

        try {
            $this->info('Limpando partidas antigas...');

            $paginaAtual = 1;

            do {
                $this->info('Buscando próximos eventos...');

                $eventos = $this->importarEventosService->eventos($paginaAtual);

                $paginaAtual++;

                if (isset($eventos['data'])) {
                    $this->info('Salvando eventos no banco de dados');

                    foreach ($eventos['data'] as $evento) {

                        $dadosEvento = [
                            'api_id' => $evento['id'],
                            'home_team' => $evento['teams']['home'],
                            'away_team' => $evento['teams']['away'],
                            'match_date' => $evento['match_date'],
                            'status' => $evento['status'],
                            'active' => (bool)$evento['active'],
                            'country' => [
                                'api_id' => $evento['league']['country']['id'],
                                'name' => $evento['league']['country']['name'],
                            ],
                            'league_data' => [
                                'api_id' => $evento['league']['id'],
                                'name' => $evento['league']['name'],
                            ]
                        ];

                        $esporte = [
                            'api_id' => $evento['sport']['id'],
                            'name' => $evento['sport']['name'],
                        ];

                        $this->eventosService->criarEvento($dadosEvento, $esporte);
                    }
                }

                $temProximaPagina = $eventos['meta']['current_page'] < $eventos['meta']['last_page'];
            } while ($temProximaPagina); // end of events loop

//            } // end of sports loop

            $this->info('Atualização finalizada!');
        } catch (\Throwable $exception) {
            // throw $exception;
            \Log::error("Erro ao atualizar eventos");
        }
    }
}
