<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\service\AppointmentSlotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/appointment')]
class AppointmentController extends AbstractController
{

    private AppointmentSlotService $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    #[Route('/', name: 'appointment_index', methods: ['GET'])]
    public function index(AppointmentRepository $appointmentRepository): Response
    {
        $user = $this->getUser();
        $appointments = $appointmentRepository->findByUserOrCreatedBy($user);

        // Define the date range for available slots
        $startDate = new \DateTime('now');
        $endDate = (clone $startDate)->modify('+1 year');
        $availableSlots = $this->slotService->getAvailableSlots($startDate, $endDate);

        return $this->render('appointment/index.html.twig', [
            'appointments' => $appointments,
            'availableSlots' => $availableSlots,
        ]);
    }


    #[Route('/new', name: 'appointment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = $appointment->getAppointmentTime();
            $slot = $this->slotService->calculateSlot($datetime);

            if ($this->slotService->isSlotAvailable($datetime)) {

                $appointment->setSlot($slot);
                $appointment->setCreatedBy($this->getUser());
                $appointment->setUser($appointment->getUser() ?: $this->getUser());
                $entityManager->persist($appointment);
                $entityManager->flush();

                $this->addFlash('success', 'Appointment successfully created!');
                return $this->redirectToRoute('appointment_index');
            } else {
                $this->addFlash('error', 'The selected slot is already taken.');
            }
        }

        return $this->render('appointment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'appointment_show', methods: ['GET'])]

    public function show(Appointment $appointment): Response
    {

        return $this->render('appointment/show.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}/edit', name: 'appointment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('appointment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'appointment_delete', methods: ['POST'])]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($appointment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('appointment_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/user', name: 'appointment_user', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function userAppointments(AppointmentRepository $appointmentRepository): Response
    {
        $user = $this->getUser();
        $appointments = $appointmentRepository->findByUserOrCreatedBy($user);

        return $this->render('appointment/user.html.twig', [
            'appointments' => $appointments,
        ]);
    }
    #[Route('/available-slots', name: 'appointment_available_slots', methods: ['GET'])]
    public function availableSlots(Request $request): Response
    {
        $start = new \DateTime('now');
        $end = (clone $start)->modify('+1 year');
        $slots = $this->slotService->getAvailableSlots($start, $end);
        // Filtrer les créneaux déjà réservés
        $availableSlots = [];
        foreach ($slots as $slot) {
            $slotTime = new \DateTime($slot['time']);
            if ($this->slotService->isSlotAvailable($slotTime)) {
                $availableSlots[] = $slot;
            }
        }
        //return $this->json(['slots' => $availableSlots]);
        return $this->json(['slots' => $slots]);
    }
}