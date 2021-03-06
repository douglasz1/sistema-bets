<?php

namespace Bets\Console\Commands;

use Illuminate\Console\Command;
use Bets\Models\User;
use Bets\Models\Client;
use Bets\Models\Bet;

class LimparCambistas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'limpar-cambistas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clears money changers who have not placed bets in the past 30 days';

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
    public function handle(){

        $this->deletarCambistas30Dias();
        $this->deletarCambistasLixeira();

    }

    private function deletarCambistas30Dias(){
        $data_limite = today()->subDays(35);

        $users = User::whereHas('roles', function ($query){
            return $query->where('name', 'seller');
        })->cursor();

        $this->info('Procurando cambistas com apostas feitas a mais de 35 dias');

        foreach($users as $user){
            //Aposta com mais de 30 dias
            $bet = Bet::where('seller_id', $user->id)->where('created_at', '>=', $data_limite)->first();
            //Apostas totais
            $qtd_bet = Bet::where('seller_id', $user->id)->count();

            if(!$bet && $qtd_bet > 0){
                Bet::where('seller_id', $user->id)->update(['client_id' => null]);

                $this->info('Deletanto Clientes...');

                Client::where('seller_id', $user->id)->delete();

                $this->info('Deletanto Cambista...');

                $user->delete();
            }
        }

        $this->info('Processamento encerrado!');
    }

    private function deletarCambistasLixeira(){

        $this->info('Procurando Cambistas na lixeira...');

        $users = User::whereHas('roles', function ($query){
            return $query->where('name', 'seller');
        })->onlyTrashed()->get();

        foreach($users as $user){

            Bet::where('seller_id', $user->id)->update(['client_id' => null]);

            $this->info('Deletanto os Clientes...');

            Client::where('seller_id', $user->id)->delete();

            $this->info('Deletanto o Cambista da lixeira...');

            $user->delete();
        }

        $this->info('Lixeira esvaziada!');
    }
}
