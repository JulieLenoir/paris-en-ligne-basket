<?php

namespace App\Controller;

use App\Service\SportRadarApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{
    private SportRadarApiService $sportRadarApi;

    public function __construct(SportRadarApiService $sportRadarApi)
    {
        $this->sportRadarApi = $sportRadarApi;
    }

    //Récupère la liste des matchs à venir
    #[Route(path: '/api/matchs', name: 'match_schedule', methods: ['GET'])]
    public function getMatchSchedule(): JsonResponse
    {
        try {
            // Récupère les données du planning des matchs à venir
            $schedule = $this->sportRadarApi->getMatchSchedule();
            
            $results = [];
            foreach ($schedule['games'] as $match) {
                if ($match['status'] != "closed"){
                    $results[] = [
                        'gameId' => $match['id'],
                        'home_team' => $match['home']['name'],
                        'away_team' => $match['away']['name'],
                        'date' => $match['scheduled'],
                        'city' => $match['venue']['city'],
                        'name' => $match['venue']['name'],
                        'capacity' => $match['venue']['capacity'],
                    ];
                }
            }
            return new JsonResponse($results);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    //Récupère les matchs terminés pour afficher les résultats
    #[Route(path: '/api/resultat', name: 'match_result', methods: ['GET'])]
    public function getResult(): JsonResponse
    {
        try {
            
            // Récupère les données du planning des matchs terminés
            $schedule = $this->sportRadarApi->getMatchSchedule();
            
            $results = [];
            foreach ($schedule['games'] as $match) {
                if ($match['status'] == "closed"){

                    $score = $this->sportRadarApi->determineWinner($match);
                    $results[] = [
                        'gameId' => $match['id'],
                        'home_team' => $match['home']['name'],
                        'home_score' => $score['home_score'],
                        'away_team' => $match['away']['name'],
                        'away_score' => $score['away_score'],
                        'date' => $match['scheduled'],
                    ];
                }            
            }

            return new JsonResponse($results);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    //Récupere les détails d'un match
    #[Route(path: '/api/matchs/{id}', name: 'match_details', methods:['GET'])]
    public function getMatchDetails(string $id): JsonResponse
    {
        try {

            $match = $this->sportRadarApi->getMatchDetails($id);
            sleep(1);
            $homeTeamDetails = $this->sportRadarApi->getTeamDetails($match['home']['id']);
            sleep(1);
            $awayTeamDetails = $this->sportRadarApi->getTeamDetails($match['away']['id']);

            $results[] = [
                'gameId' => $match['id'],
                'date' => $match['scheduled'],
                'home_team' => [
                    'name' => $match['home']['name'],
                    'alias' => $match['home']['alias'],
                    'market' => $match['home']['market'],
                    'id' => $match['home']['id'],
                    'score' =>  $match['home']['points'] ?? '',
                    'details' => [
                        'games_played' => $homeTeamDetails['own_record']['total']['games_played'], // Nombre total de matchs joués
                        'total_points' => $homeTeamDetails['own_record']['total']['points'], // Total des points marqués par l'équipe
                        'field_goals_pct' => $homeTeamDetails['own_record']['total']['field_goals_pct'], // Pourcentage de réussite aux tirs
                        'three_points_pct' => $homeTeamDetails['own_record']['total']['three_points_pct'], // Pourcentage de réussite aux tirs à 3 points
                        'rebounds' => $homeTeamDetails['own_record']['total']['rebounds'], // Nombre total de rebonds
                        'assists' => $homeTeamDetails['own_record']['total']['assists'], // Nombre total de passes décisives
                        'steals' => $homeTeamDetails['own_record']['total']['steals'], // Nombre total d'interceptions
                        'turnovers' => $homeTeamDetails['own_record']['total']['turnovers'], // Nombre total de pertes de balle
                        'efficiency' => $homeTeamDetails['own_record']['total']['efficiency'], // Indice d'efficacité globale                    ],
                    ],
                ],
                'away_team' => [
                    'name' => $match['away']['name'],
                    'alias' => $match['away']['alias'],
                    'market' => $match['away']['market'],
                    'id' => $match['away']['id'],
                    'score' => $match['away']['points'] ?? '',
                    'details' => [
                        'games_played' => $awayTeamDetails['own_record']['total']['games_played'], // Nombre total de matchs joués
                        'total_points' => $awayTeamDetails['own_record']['total']['points'], // Total des points marqués par l'équipe
                        'field_goals_pct' => $awayTeamDetails['own_record']['total']['field_goals_pct'], // Pourcentage de réussite aux tirs
                        'three_points_pct' => $awayTeamDetails['own_record']['total']['three_points_pct'], // Pourcentage de réussite aux tirs à 3 points
                        'rebounds' => $awayTeamDetails['own_record']['total']['rebounds'], // Nombre total de rebonds
                        'assists' => $awayTeamDetails['own_record']['total']['assists'], // Nombre total de passes décisives
                        'steals' => $awayTeamDetails['own_record']['total']['steals'], // Nombre total d'interceptions
                        'turnovers' => $awayTeamDetails['own_record']['total']['turnovers'], // Nombre total de pertes de balle
                        'efficiency' => $awayTeamDetails['own_record']['total']['efficiency'], // Indice d'efficacité globale                    ],
                    ],
                ],
            ];
            return new JsonResponse($results);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

     //Récupère les statistiques saisonnières d'une équipe
     #[Route(path: '/api/teams/{teamId}/stats', name: 'team_stats', methods: ['GET'])]
     public function getTeamStats(string $teamId): JsonResponse
     {
         try {
             // Récupère les statistiques saisonnières de l'équipe
             $stats = $this->sportRadarApi->getSeasonalStats($teamId);
             return new JsonResponse($stats);
         } catch (\Exception $e) {
             return new JsonResponse(['error' => $e->getMessage()], 500);
         }
     }
        
    



}
