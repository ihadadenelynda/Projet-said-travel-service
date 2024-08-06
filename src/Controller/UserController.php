<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/profile', name: 'profile_show', methods: ['GET'])]

    public function profile(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit/{id}', name: 'profile_edit', methods: ['GET', 'POST'])]

    public function edit(User $user, Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('main_home');
        }
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            // Vérifiez le mot de passe actuel
            // if($hasher->isPasswordValid($user, $form->getData()->getPassword()))
            if ($hasher->isPasswordValid($user, $currentPassword)) {
                $newPassword = $form->get('newPassword')->getData();

                // Si un nouveau mot de passe est fourni, hashez-le et mettez à jour l'utilisateur
                if ($newPassword) {
                    $hashedPassword = $hasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Votre profil a été modifié avec succès'
                );
                return $this->redirectToRoute('main_home');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe que vous avez renseigné est incorrecte'
                );
            }
        }

            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(),
            ]);

    }

    #[Route('/delete', name: 'app_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('profile_show');
    }
}