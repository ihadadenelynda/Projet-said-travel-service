<?php

namespace App\Controller;

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
use Symfony\Component\Security\Core\User\UserInterface;

class TravelController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    #[Route('/travels', name: 'list_travels')]
    public function travel(TravelRepository $travelRepository): Response
    {
        $travels = $travelRepository->findAll();
        return $this->render('travel/list.html.twig', [
            'travels' => $travels
        ]);
    }


    #[Route('/details/{id}', name: 'travel_details')]
    public function details(int $id, TravelRepository $travelRepository): Response
    {
        $travel = $travelRepository->find($id);

        if (!$travel)
        {
            throw $this->createNotFoundException('oh no ce voyage n\'existe pas!!!');
        }
        return $this->render('travel/details.html.twig', [
            "travel" => $travel
        ]);
    }

     #[Route('/travel/{id}/inscription', name:'inscription_travel')]
          public function inscription(Travel $travel, UserInterface $user): Response
      {
        $user->addTravel($travel);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
          $this->addFlash(
              'success',
              'Parfait! Vous venez de vous inscrire à notre voyage rendez vous en agence pour le paiement');

        return $this->redirectToRoute('travel_details', ['id' => $travel->getId()]);
      }

    #[Route('/travel/{id}/desinscrire', name:'desinscription_travel')]

    public function unsubscribe(Travel $travel, UserInterface $user): Response
    {
        $user->removeTravel($travel);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Vous avez reussi à vous désinscrire!'
        );

        return $this->redirectToRoute('list_travels', ['id' => $travel->getId()]);
    }

}
