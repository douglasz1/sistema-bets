<?php

namespace Bets\Console\Commands;

use Bets\Jobs\PegarBandeiraPais;
use Bets\Models\Country;
use Illuminate\Console\Command;

class BandeirasPaises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bandeiras:paises';

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
     * @return void
     */
    public function handle()
    {
        $countries = Country::all();

        foreach ($countries as $country) {
            PegarBandeiraPais::dispatch($country)->onQueue('matches');
        }
    }
}
