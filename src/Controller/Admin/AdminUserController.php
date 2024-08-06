<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin/users', name:'admin_users')]
class AdminUserController extends AbstractController
{

    #[Route('/', name: '_list')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/users/list-users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_users_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/users/newUser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: '_edit')]
    public function edit(int $id,UserRepository $userRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('L\'utilisateur n\'existe pas.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success',' L\'utilisateur a été modifié avec succès');

            return $this->redirectToRoute('admin_users_list');
        }
        return $this->render('admin/users/edit.html.twig', [
            'form'=>$form->createView(),
            'user'=>$user,
        ]);
    }


    #[Route('/delete{id}', name: '_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager,): Response
    {
        // Récupérer l'utilisateur à partir de l'id
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('L\'utilisateur n\'existe pas.');
        }
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Utiliser l'EntityManager pour supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else
            $this->addFlash('error', 'Token CSRF invalide');
            // Rediriger vers la liste des utilisateurs
            return $this->redirectToRoute('admin_users_list');
        }
}
