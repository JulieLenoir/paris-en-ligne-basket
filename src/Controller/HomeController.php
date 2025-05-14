<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(APIController $apiController): Response
    {
        // Récupérer les matchs à venir depuis l'API
        $jsonResponse = $apiController->getMatchSchedule();
        $matchsAVenir = json_decode($jsonResponse->getContent(), true);

        return $this->render('home/index.html.twig', [
            'matchs' => $matchsAVenir,
        ]);
    }
}
