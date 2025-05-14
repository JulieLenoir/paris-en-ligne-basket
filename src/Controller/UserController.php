<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SoldeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        return $this->render('user/user.html.twig', [
            'user' => $user,
        ]);
    }

    // Ajout  solde

    #[Route('/user/solde/add/{id}', name: 'user_add_solde')]
    public function addSolde(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(SoldeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Solde = $form->get('Solde')->getData();
            $user->setSolde($user->getSolde() + $Solde);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/add_solde.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //Modification solde
    #[Route('/user/update-solde/{id}', name: 'user_update_solde')]
    public function updateSolde(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }


        $form = $this->createForm(SoldeFormType::class, $user);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();


            return $this->redirectToRoute('app_user');
        }


        return $this->render('user/update_solde.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Suppression Solde
    #[Route('/user/solde/delete/{id}', name: 'user_delete_solde')]
    public function deleteSolde(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $user->setSolde(null);

        $entityManager->flush();

        return $this->redirectToRoute('app_user');
    }

}
