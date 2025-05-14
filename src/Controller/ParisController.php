<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Paris;
use App\Entity\Matchs;
use App\Form\ParisFormType;
use App\Controller\APIController;
use App\Repository\MatchsRepository;
use App\Service\SportRadarApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ParisController  extends AbstractController
{

    #[Route('/paris/create/{matchId}', name: 'paris_create_form', methods: ['GET', 'POST'])]
    public function createBet(
        string $matchId,
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        SportRadarApiService $sportRadarApi,
        MatchsRepository $matchsRepository
    ): Response {
        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si le match existe en base
        $match = $matchsRepository->findOneBy(['Game_id' => $matchId]);

        if (!$match) {
            // Récupérer les infos du match via l'API
            $matchData = $sportRadarApi->getMatchDetails($matchId);
            if (!$matchData) {
                throw $this->createNotFoundException('Match introuvable.');
            }

            // Créer et sauvegarder le match en base
            $match = new Matchs();
            $match->setGameId($matchData['id']);
            $match->setHomeTeamName($matchData['home']['name']);
            $match->setAwayTeamName($matchData['away']['name']);
            $match->setScheduledTime($matchData['scheduled']);

            $em->persist($match);
            $em->flush();
        }

        // Création du formulaire de pari
        $pari = new Paris();
        $pari->setMatch($match);
        $pari->setUser($user);

        $form = $this->createForm(ParisFormType::class, $pari, [
            'match' => $match,
            'user_solde' => $user->getSolde(), // Correction ici
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifier que l'utilisateur a suffisamment de solde
            $mise = $form->get('mise')->getData();
            if ($mise > $user->getSolde()) {

                $this->addFlash('error', 'Votre solde est insuffisant.');
                return $this->redirectToRoute('paris_create_form', ['matchId' => $matchId]);
            }

           // Déduire la mise du solde de l'utilisateur
            $user->setSolde($user->getSolde() - $mise);

            // Enregistrer le pari
            $pari->setStatut('en cours');
            $pari->setMise($mise);
            $pari->setGains(0);
            $pari->setPerte(0);
            $pari->setSoldeCloture($pari->getUser()->getSolde());

            $em->persist($pari);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Pari enregistré avec succès !');
            return $this->redirectToRoute('paris_list');
        }

        // **Ajout du return manquant**
        return $this->render('paris/create.html.twig', [
            'form' => $form->createView(),
            'match' => $match,
        ]);
    }





    #[Route('/paris', name: 'paris_list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $paris = $em->getRepository(Paris::class)->findAllWithRelations();



        return $this->render('paris/index.html.twig', [
            'result' => $paris,
        ]);
    }


    #[Route('/paris/{id}', name: 'paris_show', methods: ['GET'])]
    public function show($id, EntityManagerInterface $em): JsonResponse
    {
        $pari = $em->getRepository(Paris::class)->find($id);

        if (!$pari) {
            return $this->json(['error' => 'Paris not found'], 404);
        }

        return $this->json([
            'id' => $pari->getId(),
            'statut' => $pari->getStatut(),
            'gains' => $pari->getGains(),
            'perte' => $pari->getPerte(),
            'mise' => $pari->getMise(),
            'user_id' => $pari->getUser() ? $pari->getUser()->getId() : null,
            'match_id' => $pari->getMatch() ? $pari->getMatch()->getId() : null,
        ]);
    }


// private function updateParis(Matchs $match, EntityManagerInterface $entityManager)
// {
//     // Récupérer les paris associés à ce match
//     $parisRepository = $entityManager->getRepository(Paris::class);
//     $paris = $parisRepository->findBy(['match' => $match]);

//     // Vérifier les résultats pour chaque pari
//     foreach ($paris as $pari) {
//         // Vérifier si le pari est sur la bonne équipe
//         $user = $pari->getUser();
//         $mise = $pari->getMise();
//         $gagnant = null;

//         // Comparer les scores et déterminer si le pari est gagné ou perdu
//         if ($pari->getEquipe() === 'home' && $match->getHomePoints() > $match->getAwayPoints()) {
//             $gagnant = 'home';
//         } elseif ($pari->getEquipe() === 'away' && $match->getAwayPoints() > $match->getHomePoints()) {
//             $gagnant = 'away';
//         }

//         // Si l'équipe sur laquelle l'utilisateur a parié a gagné
//         if ($gagnant) {
//             $pari->setStatut('terminé');
//             $pari->setGains($mise * 2);  // Le gain est la mise * 2
//             $user->setSolde($user->getSolde() + ($mise * 2));  // Mise à jour du solde de l'utilisateur
//         } else {
//             // Si l'utilisateur a perdu le pari
//             $pari->setStatut('terminé');
//             $pari->setPerte($mise);  // La perte est égale à la mise
//             $user->setSolde($user->getSolde() - $mise);  // Mise à jour du solde de l'utilisateur
//         }

//         $entityManager->persist($pari);
//         $entityManager->persist($user);  // Ne pas oublier de persister l'utilisateur
//     }
// }

    #[Route('/paris/delete/{id}', name: 'paris_delete', methods: ['POST'])]
    public function delete($id, Request $request, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 403);
        }

        $pari = $em->getRepository(Paris::class)->find($id);

        if (!$pari) {
            return $this->json(['error' => 'Pari non trouvé'], 404);
        }

        // Vérifier si le pari appartient bien à l'utilisateur connecté
        if ($pari->getUser() !== $user) {
            return $this->json(['error' => 'Vous ne pouvez pas supprimer ce pari'], 403);
        }

        // Ajouter la mise au solde de l'utilisateur
        $user->setSolde($user->getSolde() + $pari->getMise());

        // Supprimer le pari
        $em->remove($pari);
        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Pari supprimé et mise remboursée']);
    }

}
