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

class TravelController extends AbstractController
{
    #[Route('/travels', name: 'list_travels')]
    public function travel(TravelRepository $travelRepository): Response
    {
        $travels = $travelRepository->findAll();
        return $this->render('travel/list.html.twig', [
            'travels' => $travels
        ]);
    }


    #[Route('/details/{id}', name: 'travel_details')]

    public function details(int $id,TravelRepository $travelRepository): Response
    {
        $travel = $travelRepository->find($id);

        if(!$travel) {
            throw $this ->createNotFoundException('oh no!!!');

        }

        return $this->render('travel/details.html.twig',[
            "travel" => $travel
        ]);
    }

    #[Route('/travels/create', name: 'creation_travels')]
    public function create(Request $request,
                           EtatRepository $etatRepository,
                           EntityManagerInterface $entityManager,
                           UserRepository $userRepository,
    ) :Response
    {
        $travel = new Travel();

        $travelForm = $this->createForm(TravelType::class, $travel);
        $travelForm->handleRequest($request);

        if($travelForm->isSubmitted()&& $travelForm->isValid()) {
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



}
