<?php

namespace Bets\Services;

use GuzzleHttp\Client;

/**
 * Get JSON data from URL and convert to array
 */
class GetJsonService
{
    private $apiURI;
    private $apiToken;

    public function __construct()
    {
        $this->apiURI = env('API_URI');
        $this->apiToken = env('API_TOKEN');
    }

    private function getRequest(string $url, array $query = [])
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
                'result' => 'error',
                'results' => [],
            ];

        } catch (\Throwable $exception) {
            return [
                'result' => 'error',
                'results' => [],
                'message' => $exception->getMessage()
            ];
        }
    }

    /**
     * @param int $page
     * @param int $sportId
     * @return array|mixed
     */
    public function getUpcoming($page = 1, $sportId = 1)
    {
        return $this->getRequest('matches', [
            'sport_id' => $sportId,
            'page' => $page
        ]);
    }

    /**
     * @param int $eventId
     * @return array|mixed
     */
    public function getEvent(int $eventId)
    {
        return $this->getRequest("matches/{$eventId}");
    }

    /**
     * @param int $eventId
     * @return array|mixed
     */
    public function getResult(int $eventId)
    {
        return $this->getRequest("matches/{$eventId}/result");
    }

    public function partidasFutebol($page = 1)
    {
        return $this->getRequest('partidas', ['page' => $page]);
    }

    public function partida($partidaId)
    {
        return $this->getRequest("partidas/resultado/{$partidaId}");
    }
}
