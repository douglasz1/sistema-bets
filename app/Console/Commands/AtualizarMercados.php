<?php

namespace Bets\Console\Commands;

use Bets\Models\Esportes\CategoriaCotacao;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class AtualizarMercados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mercados:atualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client(['base_uri' => 'https://portal.simuladoraqui.com.br']);

        $result = $client->get('storage/categorias.json', []);

        if ($result->getStatusCode() == 200) {
            $categorias = json_decode($result->getBody(), true);

            # CategoriaCotacao::where('id', '>=', 1)->delete();

            foreach ($categorias as $categoria) {
                $excluir = [
                    'jogador-que-marca-1o-gol',
                    'jogador-que-marca-qualquer-momento',
                    'jogador-que-marca-ultimo-gol',
                ];

                if (in_array($categoria['mercado'], $excluir)) {
                    continue;
                }

                CategoriaCotacao::query()->firstOrCreate($categoria);
            }

            cache()->tags('categoriasCotacao')->flush();

            cache()->tags('quotationNames')->flush();
        }

        return true;
    }
}
