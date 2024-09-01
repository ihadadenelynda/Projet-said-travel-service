<?php

namespace App\Controller\Admin;

use App\Entity\Travel;
use App\Form\TravelType;
use App\Repository\EtatRepository;
use App\Repository\TravelRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/travels', name: 'admin_travels')]
class AdminTravelController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(TravelRepository $travelRepository): Response
    {
        $travels = $travelRepository->findAll();
        return $this->render('admin/travels/index.html.twig', [
            'travels' => $travels
            ]);
    }

    #[Route('/{id}/users', name: '_users')]
    public function listUsersByTravel(int $id,TravelRepository $travelRepository): Response
    {
        $travel= $travelRepository->find($id);
        if (!$travel) {
            throw $this->createNotFoundException('Le voyage n\'existe pas.');
        }
        return $this->render('admin/travels/list-travelers.html.twig', [
            'travel'=>$travel,
        ]);
    }


    #[Route('/create', name: '_creation_travels')]
    public function create(Request                $request,
                           EtatRepository         $etatRepository,
                           EntityManagerInterface $entityManager,
    ): Response
    {
        $travel = new Travel();

        $travelForm = $this->createForm(TravelType::class, $travel);
        $travelForm->handleRequest($request);

        if ($travelForm->isSubmitted() && $travelForm->isValid()) {
            $travel->setEtat($etatRepository->find($travel->getEtat()->getId()));

            $entityManager->persist($travel);
            $entityManager->flush();

            $this->addFlash('success', 'Voyage créé !');
            return $this->redirectToRoute('list_travels');
        }
        return $this->render('travel/create.html.twig', [
            'travelForm' => $travelForm->createView()
        ]);
    }


    #[Route('/travels/{id}/edit', name: '_edit', methods:['GET', 'POST'])]
    public function edit(int $id,TravelRepository $travelRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $travel = $travelRepository->find($id);

        if (!$travel) {
            throw $this->createNotFoundException('Le voyage n\'existe pas.');
        }

        $form = $this->createForm(TravelType::class, $travel);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $entityManager->persist($travel);
            $entityManager->flush();

            $this->addFlash('success',' Le voyage a été modifié avec succès');

            return $this->redirectToRoute('list_travels');
        }
        return $this->render('travel/edit.html.twig', [
            'form'=>$form->createView(),
            'travel'=>$travel,
        ]);
    }



    #[Route('/{id}/delete', name: '_travel.delete',methods:['DELETE'])]
    public function delete(Travel $travel,Request $request,EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$travel->getId(), $request->request->get('_token'))) {
            $entityManager->remove($travel);
            $entityManager->flush();

            $this->addFlash('success', 'Voyage supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la validation du token CSRF.');
        }
        return $this->redirectToRoute('list_travels');

    }
}
