<?php


namespace Bets\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class LiveMatchesService
{
    private $apiURI;

    public function __construct()
    {
        $this->apiURI = env('API_URI');
    }

    public function aoVivo($partidaID)
    {
        $client = new Client([
            'base_uri' => $this->apiURI
        ]);

        $result = $client->get('partidas/ao-vivo', [
            'query' => [
                'partida' => $partidaID
            ]
        ]);

        if ($result->getStatusCode() == 200) {
            $resposta = json_decode($result->getBody(), true);
            return $resposta['evento'];
        }

        return [];
    }

    public function quotations($matchId)
    {
        $client = new Client([
            'base_uri' => $this->apiURI
        ]);

        $result = $client->get("live/{$matchId}");

        if ($result->getStatusCode() == 200) {
            return json_decode($result->getBody(), true);
        }

        return [];
    }

    public function multipleMatches(array $partidas)
    {
        $client = new Client([
            'base_uri' => $this->apiURI
        ]);

        $matches = collect([]);

        $requests = function ($partidas) {
            $quantidadePartidas = count($partidas);
            $uri = $this->apiURI . 'live';

            for ($i = 0; $i < $quantidadePartidas; $i++) {
                yield new Request('GET', "{$uri}/{$partidas[$i]}");
            }
        };

        $pool = new Pool($client, $requests($partidas), [
            'concurrency' => 5,
            'fulfilled' => function ($response) use ($matches) {
                // this is delivered each successful response
                $result = json_decode($response->getBody(), true);
                $matches->push($result);
            }
//            ,
//            'rejected' => function ($reason, $index) {
//                dd($reason, $index);
//                // this is delivered each failed request
//            },
        ]);

        $promise = $pool->promise();

        $promise->wait();

        return $matches;
    }
}
