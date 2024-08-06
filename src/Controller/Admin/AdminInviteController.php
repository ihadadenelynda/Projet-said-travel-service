<?php

namespace App\Controller\Admin;

use App\Entity\Invite;
use App\Form\InviteType;
use App\Repository\InviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/invite')]
class AdminInviteController extends AbstractController
{
    #[Route('/', name: 'app_admin_invite_index', methods: ['GET'])]
    public function index(InviteRepository $inviteRepository): Response
    {
        return $this->render('admin_invite/index.html.twig', [
            'invites' => $inviteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_invite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invite = new Invite();
        $form = $this->createForm(InviteType::class, $invite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($invite);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_invite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_invite/new.html.twig', [
            'invite' => $invite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_invite_show', methods: ['GET'])]
    public function show(Invite $invite): Response
    {
        return $this->render('admin_invite/show.html.twig', [
            'invite' => $invite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_invite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invite $invite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InviteType::class, $invite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_invite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_invite/edit.html.twig', [
            'invite' => $invite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_invite_delete', methods: ['POST'])]
    public function delete(Request $request, Invite $invite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($invite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_invite_index', [], Response::HTTP_SEE_OTHER);
    }
}
