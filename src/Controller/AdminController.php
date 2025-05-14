<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SportRadarApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class AdminController extends AbstractController
{
    private SportRadarApiService $sportRadarApi;

    public function __construct(SportRadarApiService $sportRadarApi)
    {
        $this->sportRadarApi = $sportRadarApi;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les utilisateurs avec leurs paris
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,  // Passe les utilisateurs à la vue Twig
        ]);
    }


    #[Route('/teams', name: 'admin_teams_stats')]
    public function teamsStats(): Response
    {
        try {
            // Liste des équipes
            $teamsList = [
                '179339ce-525f-4a2d-8117-a4b33b3daebb' => 'Phoenix Mercury',
                '67a79348-041a-45f6-bb12-722d38dc8dbf' => 'Delaware Blue Coats',
                'b971b187-c542-4b76-9548-114acc564e05' => 'Kings Stockton',

            ];
            
            $teamsStats = [];
    
            foreach ($teamsList as $teamId => $teamName) {
                $stats = $this->sportRadarApi->getSeasonalStats($teamId);
                dump($stats); // Affiche la réponse de l'API
            
                // Vérification des données reçues
                if (!isset($stats['own_record']['total']) || !isset($stats['own_record']['average'])) {
                    throw new \Exception("Données manquantes pour l'équipe $teamName (ID: $teamId)");
                }
            
                // Ajout des stats de l'équipe
                // Ajout des stats de l'équipe dans le tableau
                $teamsStats[$teamId] = [
                    'team_id' => $stats['id'] ?? '',
                    'team_name' => $stats['name'] ?? '',
                    'team_market' => $stats['market'] ?? '',
                    'stats' => [
                        'games_played' => $stats['own_record']['total']['games_played'] ?? 0,
                        'points' => $stats['own_record']['total']['points'] ?? 0,
                        'field_goals_made' => $stats['own_record']['total']['field_goals_made'] ?? 0,
                        'three_point_pct' => $stats['own_record']['total']['three_points_pct'] ?? 0,
                        'total_rebounds' => $stats['own_record']['total']['total_rebounds'] ?? 0,
                    ],
                ];
            }
            
    
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des statistiques : ' . $e->getMessage());
    
            // On garde la structure, mais avec des valeurs neutres
            $teamsStats = [];
        }
    
        // Rendu avec les données, même en cas d'erreur
        return $this->render('admin/admindetails.html.twig', [
            'teams_stats' => $teamsStats,
        ]);
    }
    

    
}