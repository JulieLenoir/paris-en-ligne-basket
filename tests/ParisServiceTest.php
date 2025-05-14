<?php

namespace App\Tests\Service;

use App\Entity\Paris;
use App\Entity\Matchs;
use App\Entity\User;
use App\Service\ParisService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ParisServiceTest extends TestCase
{
    private $entityManager;
    private $parisRepository;
    private $parisService;

    protected function setUp(): void
    {
        // Mock de EntityManagerInterface
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Mock de Repository pour les paris
        $this->parisRepository = $this->createMock(EntityRepository::class);

        // Simule la récupération du repository Paris
        $this->entityManager
            ->method('getRepository')
            ->with(Paris::class)
            ->willReturn($this->parisRepository);

        // Instanciation du service avec le mock de EntityManager
        $this->parisService = new ParisService($this->entityManager);
    }

    public function testUpdateParis_WinningBet()
    {
        // Création d'un match terminé
        $match = new Matchs();
        $match->setHomeTeamName('Lakers');
        $match->setAwayTeamName('Heat');
        $match->setHomePoints(100);
        $match->setAwayPoints(90);

        // Création d'un utilisateur fictif
        $user = new User();
        $user->setSolde(100);

        // Création d'un pari gagnant
        $pari = new Paris();
        $pari->setUser($user);
        $pari->setMise(50);
        $pari->setEquipe('Lakers'); // L'équipe gagnante du match

        // Simule la récupération des paris liés au match
        $this->parisRepository
            ->method('findBy')
            ->with(['match' => $match])
            ->willReturn([$pari]);

        // Mock de `persist()` et `flush()` pour éviter toute interaction réelle avec la BDD
        $this->entityManager
            ->expects($this->exactly(2)) // Un appel pour $pari et un autre pour $user
            ->method('persist');

        $this->entityManager
            ->expects($this->once()) // Un seul flush à la fin
            ->method('flush');

        // Exécution du service
        $this->parisService->updateParis($match);

        // Assertions
        $this->assertEquals(200, $user->getSolde()); // Solde après gain (100 + 50*2)
        $this->assertEquals(100, $pari->getGains()); // Gain calculé (50 * 2)
        $this->assertEquals('Gagné', $pari->getStatut());
        $this->assertEquals(200, $pari->getSoldeCloture()); // Vérification du solde final
    }

    public function testUpdateParis_LosingBet()
    {
        // Création d'un match terminé
        $match = new Matchs();
        $match->setHomeTeamName('Lakers');
        $match->setAwayTeamName('Heat');
        $match->setHomePoints(100);
        $match->setAwayPoints(90);

        // Création d'un utilisateur fictif
        $user = new User();
        $user->setSolde(100);

        // Création d'un pari perdant
        $pari = new Paris();
        $pari->setUser($user);
        $pari->setMise(50);
        $pari->setEquipe('Heat'); // Mauvais choix

        // Simule la récupération des paris liés au match
        $this->parisRepository
            ->method('findBy')
            ->with(['match' => $match])
            ->willReturn([$pari]);

        // Mock de `persist()` et `flush()` pour éviter toute interaction réelle avec la BDD
        $this->entityManager
            ->expects($this->exactly(2)) // Un appel pour $pari et un autre pour $user
            ->method('persist');

        $this->entityManager
            ->expects($this->once()) // Un seul flush à la fin
            ->method('flush');

        // Exécution du service
        $this->parisService->updateParis($match);

        // Assertions
        $this->assertEquals(100, $user->getSolde()); // Solde inchangé
        $this->assertEquals(50, $pari->getPerte()); // Perte égale à la mise
        $this->assertEquals('Perdu', $pari->getStatut());
        $this->assertEquals(100, $pari->getSoldeCloture()); // Vérification du solde final
    }
}
