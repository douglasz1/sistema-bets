<?php

namespace Bets\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class PegarBandeiraPais implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $country;
    private $apiURI;

    /**
     * Create a new job instance.
     *
     * @param $country
     */
    public function __construct($country)
    {
        $this->country = $country;
        $this->apiURI = env('API_URI');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = new Client([
                'base_uri' => $this->apiURI
            ]);

            $resource = storage_path("app/public/flags/{$this->country->api_id}.svg");

            $client->get("partidas/bandeira-pais/{$this->country->api_id}", ['sink' => $resource]);

        } catch (\Throwable $exception) {
            Log::error("Erro ao buscar bandeira do país: {$this->country->name}");
            // throw $exception;
        }
    }
}
