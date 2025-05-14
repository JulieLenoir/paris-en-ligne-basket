<?php

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testAuthenticationRequiredForBetting()
    {
        $client = static::createClient();

        // Tentative d'accès à la page de profil sans être authentifié
        $client->request('GET', '/user');

        // Vérifier que l'utilisateur est redirigé vers la page de connexion
        $this->assertResponseRedirects('/login');
    }

    public function testAccessAfterLogin()
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);

        // Vérifier si l'utilisateur existe déjà
        $testUser = $userRepository->findOneBy(['email' => 'test@example.com']);
        if (!$testUser) {
            $testUser = new User();
            $testUser->setEmail('test@example.com');
            $testUser->setRoles(['ROLE_USER']);

            // Hasher le mot de passe
            $passwordHasher = static::getContainer()->get('security.password_hasher');
            $testUser->setPassword($passwordHasher->hashPassword($testUser, 'password123'));

            $entityManager->persist($testUser);
            $entityManager->flush();
        }

        // Vérifier que l'utilisateur existe bien
        $this->assertNotNull($testUser, "L'utilisateur test@example.com n'a pas été trouvé en base.");

        // Simuler la connexion
        $client->loginUser($testUser);

        // Accéder à la page utilisateur après authentification
        $client->request('GET', '/user');

        // Vérifier que la page est bien accessible
        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => 'test@example.com']);
        if ($testUser) {
            $entityManager->remove($testUser);
            $entityManager->flush();
        }
        parent::tearDown();
    }
}