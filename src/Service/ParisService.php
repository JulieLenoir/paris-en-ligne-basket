<?php

// src/Service/BetService.php

namespace App\Service;

use App\Entity\Paris;
use App\Entity\Matchs;
use Doctrine\ORM\EntityManagerInterface;

class ParisService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Met à jour les paris en fonction des résultats du match
     * @param Matchs $match
     */
    public function updateParis(Matchs $match)
    {
        // Récupérer les paris associés à ce match
        $parisRepository = $this->entityManager->getRepository(Paris::class);
        $paris = $parisRepository->findBy(['match' => $match]);

        // Vérifier les résultats pour chaque pari
        foreach ($paris as $pari) {
            // Vérifier si le pari est sur la bonne équipe
            $user = $pari->getUser();
            $mise = $pari->getMise();

            $gagnant = null;
            if ($match->getHomePoints() > $match->getAwayPoints()) {
                $gagnant = $match->getHomeTeamName();

            }else {
                $gagnant = $match->getAwayTeamName();
            }

            // Si l'équipe sur laquelle l'utilisateur a parié a gagné
            if ($pari->getEquipe() === $gagnant) {

                $pari->setGains($mise * 2);  // Le gain est la mise * 2
                $user->setSolde($user->getSolde() +  $pari->getGains());  // Mise à jour du solde de l'utilisateur
                $pari->setStatut('Gagné');
                $pari->setSoldeCloture($pari->getUser()->getSolde());
            } else {
                // Si l'utilisateur a perdu le pari
                $pari->setStatut('Perdu');
                $pari->setPerte($mise);  // La perte est égale à la mise
                $pari->setSoldeCloture($pari->getUser()->getSolde());
            }

            // Persister les entités modifiées
            $this->entityManager->persist($pari);
            $this->entityManager->persist($user);  // Ne pas oublier de persister l'utilisateur
        }

        // Flush à la fin pour éviter les appels multiples à la base
        $this->entityManager->flush();
    }
}
