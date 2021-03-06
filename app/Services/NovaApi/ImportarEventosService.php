<?php


namespace Bets\Services\NovaApi;


use GuzzleHttp\Client;

class ImportarEventosService
{
    private $apiURI;
    private $apiToken;

    public function __construct()
    {
        $this->apiURI = env('API_URI');
        $this->apiToken = env('API_TOKEN');
    }

    private function fazerRequisicao(string $url, array $query = [])
    {
        try {
            $client = new Client([
                'base_uri' => $this->apiURI
            ]);

            $result = $client->get($url, [
                'query' => $query
            ]);

            if ($result->getStatusCode() == 200) {
                return json_decode($result->getBody(), true);
            }

            return [
                'resultado' => 'error',
                'data' => [],
            ];

        } catch (\Throwable $exception) {
            return [
                'resultado' => 'error',
                'data' => [],
                'message' => $exception->getMessage()
            ];
        }
    }

    public function eventos($page = 1)
    {
        return $this->fazerRequisicao('partidas', ['page' => $page]);
    }

    public function evento($partidaId)
    {
        return $this->fazerRequisicao("partidas/{$partidaId}");
    }

    public function resultado(int $eventoId)
    {
        return $this->fazerRequisicao("partidas/{$eventoId}/resultado");
    }
}
