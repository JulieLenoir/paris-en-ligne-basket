<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SportRadarApiService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;

    }

    /**
     * Récupère les informations des matchs (passés ou à venir).
     * @return array
     */
    public function getMatchSchedule(): array
    {
        $endpoint = sprintf('%s/nbdl/trial/v8/en/games/2024/REG/schedule.json', $this->baseUrl);

        $response = $this->httpClient->request('GET', $endpoint, [
            'query' => [
                'api_key' => $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }


    //Recupere le detail d'un match
    public function getMatchDetails(string $matchId): array
    {
        $endpoint = sprintf('%s/nbdl/trial/v8/en/games/%s/pbp.json',$this->baseUrl, $matchId);
        $response = $this->httpClient->request('GET', $endpoint, [
            'query' => [
                'api_key' => $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }


    public function getTeamDetails(string $teamId): array
    {
        $endpoint = sprintf('%s/nbdl/trial/v8/en/seasons/2024/REG/teams/%s/statistics.json', $this->baseUrl, $teamId);

        $response = $this->httpClient->request('GET', $endpoint, [
            'query' => [
                'api_key' => $this->apiKey,
            ],
        ]);

        return $response->toArray();
    }

    public function getSeasonalStats(string $teamId, string $season = '2024', string $seasonType = 'REG'): array
    {
        $endpoint = sprintf(
            '%s/nbdl/trial/v8/en/seasons/%s/%s/teams/%s/statistics.json',
            $this->baseUrl,
            $season,
            $seasonType,
            $teamId
        );

        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'query' => [
                    'api_key' => $this->apiKey,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new \Exception("Erreur API SportRadar: Code $statusCode");
            }

            return $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des stats de l'équipe $teamId: " . $e->getMessage());
        }
    }





    /**
     * Analyse le gagnant d'un match donné.
     *
     * @param array $match Les informations d'un match (équipes, scores).
     * @return array Résultat du match.
     */
    public function determineWinner(array $match): array
    {
        $homeScore = $match['home_points'] ?? 0;
        $awayScore = $match['away_points'] ?? 0;

        return [
            'home_score' => $homeScore,
            'away_score' => $awayScore,
        ];

    }

}
