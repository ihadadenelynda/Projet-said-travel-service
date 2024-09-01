<?php

namespace App\Form;

use App\Entity\Appointment;
use App\service\AppointmentSlotService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    private $slotService;

    public function __construct(AppointmentSlotService $slotService)
    {
        $this->slotService = $slotService;
    }

        public function buildForm(FormBuilderInterface $builder, array $options):void
    {

        $builder
            ->add('firstName', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'mapped' => false,
                'required' => true,
            ])

            ->add('motif', ChoiceType::class, [
                'choices' => [
                    'Paiement' => 'Paiement',
                    'Reclamation' => 'Reclamation',
                    'Renseignement' => 'Renseignement',
                    'Reservation' => 'Reservation',
                    ]
            ]);
        // Ajout dynamique des créneaux horaires disponibles
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                // Par défaut, on utilise la plage de temps pour calculer les créneaux
                $startDate = new \DateTime('now');
                $endDate = (clone $startDate)->modify('+1 week'); // Ajustez la période si nécessaire
                $availableSlots = $this->slotService->getAvailableSlots($startDate, $endDate);

                    $choices = [];
                    foreach ($availableSlots as $slot) {
                        $choices[$slot['time']] = $slot['time'];
                    }

                    $form->add('slot', ChoiceType::class, [
                        'label' => 'Slot',
                        'choices' => $choices,
                        'required' => true,
                    ]);
            });

         $builder->add('appointmentTime', DateTimeType::class, [
             'label' => 'Appointment Datetime',
             'widget' => 'single_text',
         ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
