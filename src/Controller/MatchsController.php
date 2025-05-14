<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Service\ParisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MatchsController extends AbstractController
{
    #[Route('/matchs', name: 'app_matchs')]
public function index(APIController $apiController, EntityManagerInterface $entityManager, ParisService $ParisService): Response
{
    if ($this->getUser()) {
        // Appel de l'API interne pour récupérer les matchs à venir
        $jsonResponse = $apiController->getMatchSchedule();
        $matchsAVenir = json_decode($jsonResponse->getContent(), true);

        // Appel de l'API pour récupérer les résultats des matchs passés
        $jsonResultResponse = $apiController->getResult();
        $matchsPasse = json_decode($jsonResultResponse->getContent(), true);

        // Récupérer tous les matchs déjà présents en base pour éviter les requêtes multiples
        $existingMatches = $entityManager->getRepository(Matchs::class)->findAll();

    // Créer un tableau de correspondance par gameId pour un accès rapide
    $existingMatchesByGameId = [];
    foreach ($existingMatches as $match) {
        $existingMatchesByGameId[$match->getGameId()] = $match;
    }

    // Scanne tous les matchs terminés
    foreach ($matchsPasse as $match) {
        if (isset($match['gameId'])) {
            // Vérifier si le match existe déjà en base
            if (isset($existingMatchesByGameId[$match['gameId']])) {
                $existingMatch = $existingMatchesByGameId[$match['gameId']];
                if ($existingMatch->getHomePoints()== null &&  $existingMatch->getAwayPoints()== null) {
                // Mettre à jour les scores
                $existingMatch->setHomePoints($match['home_score']);
                $existingMatch->setAwayPoints($match['away_score']);
                $existingMatch->setScheduledTime($match['date']);  // Si tu veux aussi mettre à jour la date
                 // Appeler le service pour mettre à jour les paris
                $ParisService->updateParis($existingMatch);
                }else {
                    continue;
                }

            }
        } else {
            // Si le match n'a pas de gameId, mettre une alerte pour l'utilisateur
            $this->addFlash('error', 'Erreur dans la récupération des résultats du match.');


            continue;
        }

    }

        // Scanne tous les matchs terminés
        foreach ($matchsPasse as $match) {
            if (isset($match['gameId'])) {
                // Vérifier si le match existe déjà en base
                if (isset($existingMatchesByGameId[$match['gameId']])) {
                    $existingMatch = $existingMatchesByGameId[$match['gameId']];

                    // Mettre à jour les scores
                    $existingMatch->setHomePoints($match['home_score']);
                    $existingMatch->setAwayPoints($match['away_score']);
                    $existingMatch->setScheduledTime($match['date']);  // Si tu veux aussi mettre à jour la date

                    // Appeler le service pour mettre à jour les paris
                    $ParisService->updateParis($existingMatch);
                }
            } else {
                // Logique d'erreur si gameId est absent
                // Par exemple, tu peux logger l'erreur ou gérer autrement
                continue;
            }
        }

        // Exécute les mises à jour en base
        $entityManager->flush();

        // Retourner la vue avec les matchs
        return $this->render('matchs/index.html.twig', [
            'matchs' => $matchsAVenir,
            'matchs_passe' => $matchsPasse,
        ]);
    } else {
        //redirige au login
        return $this->redirectToRoute('login');
    }
}


    #[Route('/matchs/{id}', name: 'app_matchs_details')]
    public function details(APIController $apiController, string $id): Response
    {
        // Appel de l'API interne
        $jsonResponse = $apiController->getMatchDetails($id);

        // Décodage de la réponse JSON en tableau PHP
        $detailsMatch= json_decode($jsonResponse->getContent(), true);

        if (!$detailsMatch) {
            throw $this->createNotFoundException("Détails du match introuvables.");
        }
        $match = $detailsMatch[0];
        return $this->render('matchs/detail.html.twig', [
            'match' => $match,
        ]);
    }

}