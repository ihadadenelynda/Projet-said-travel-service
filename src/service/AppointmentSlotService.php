<?php

namespace App\service;

use App\Repository\AppointmentRepository;
use DateTime;

class AppointmentSlotService
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function getAvailableSlots(DateTime $startDate, DateTime $endDate): array
    {
        $slots = [];
        $current = clone $startDate;
        $endDate = (clone $startDate)->modify('+1 week'); // Réduire la plage pour les tests

        while ($current < $endDate) {
            if ($current->format('N') < 6) { // Exclure les weekends (Samedi=6, Dimanche=7)
                $slotTime = clone $current;
                while ($slotTime->format('H:i') < '16:00') {
                    if ($this->isSlotAvailable($slotTime)) {
                        $slots[] = [
                            'time' => $slotTime->format('Y-m-d H:i'),
                            'slot' => $this->calculateSlot($slotTime)
                        ];
                    }
                    $slotTime->modify('+30 minutes');//Définir des créneaux de 30 minutes
                }
            }
            $current->modify('+1 day')->setTime(9, 0);//Passer au jour suivant à 9h
        }

        return $slots;
    }


    /**
     * Vérifie si un créneau horaire est disponible.
     *
     * @param DateTime $slotTime
     * @return bool
     */

    public function isSlotAvailable(DateTime $slotTime): bool
    {
        // Convert to the beginning of the slot (if it's not already)
        $start = $slotTime->format('Y-m-d H:i');
        $end = (clone $slotTime)->modify('+30 minutes')->format('Y-m-d H:i');

        return !$this->appointmentRepository->findOneBySlot($start, $end);
    }

    /**
     * Calcule et formate le créneau horaire.
     *
     * @param DateTime $datetime
     * @return string
     */

   public function calculateSlot(DateTime $datetime): string
    {
       return $datetime->format('Y-m-d H:i');
    }
}