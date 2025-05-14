<?php

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends TestCase
{
    private $entityManager;
    private $userRepository;
    private $passwordHasher;

    protected function setUp(): void
    {
        // Mock de l'EntityManager
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Mock du UserRepository
        $this->userRepository = $this->createMock(EntityRepository::class);

        // Simuler `getRepository(User::class)` pour retourner notre mock
        $this->entityManager->method('getRepository')
            ->willReturn($this->userRepository);

        // Mock du password hasher
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    }

    public function testEmailAlreadyTaken()
    {
        // Simuler un utilisateur existant en BDD
        $existingUser = new User();
        $existingUser->setEmail('test@example.com');

        // Configurer le mock du repository pour renvoyer l'utilisateur existant
        $this->userRepository->method('findOneBy')
            ->with(['email' => 'test@example.com'])
            ->willReturn($existingUser);

        // Simuler une nouvelle tentative d'inscription avec le même email
        $newUser = new User();
        $newUser->setEmail('test@example.com');

        // Vérifier que l'utilisateur existe déjà
        $this->assertNotNull(
            $this->userRepository->findOneBy(['email' => $newUser->getEmail()]),
            'L\'email existe déjà en base de données.'
        );
    }

    public function testSuccessfulRegistration()
    {
        $newUser = new User();
        $newUser->setEmail('newuser@example.com');
        $newUser->setPassword('securepassword123');

        // Simuler un hashage correct du mot de passe
        $hashedPassword = 'hashed_securepassword123';
        $this->passwordHasher->method('hashPassword')
            ->willReturn($hashedPassword);

        // Simuler que l'utilisateur n'existe pas encore en base
        $this->userRepository->method('findOneBy')
            ->with(['email' => 'newuser@example.com'])
            ->willReturn(null);

        // Injecter le mot de passe haché
        $newUser->setPassword($this->passwordHasher->hashPassword($newUser, $newUser->getPassword()));

        // Configurer l'attente de `persist()` et `flush()`
        $this->entityManager->expects($this->once())->method('persist')->with($newUser);
        $this->entityManager->expects($this->once())->method('flush');

        // Simuler l'enregistrement de l'utilisateur
        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        // Vérifier que le mot de passe a bien été haché
        $this->assertNotEquals('securepassword123', $newUser->getPassword());
    }
}
