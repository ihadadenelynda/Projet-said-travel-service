<?php

namespace App\Form;

use App\Entity\Invite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class,[
                'label'=>'Prénom',
            ])
            ->add('lastName',TextType::class,[
                'label'=>'Nom'
            ])
            ->add('appointmentTime', DateTimeType::class, [
                'label' => 'Date et Heure',
                'widget' => 'single_text',
        ])
            ->add('motif', ChoiceType::class, [
                'choices' => [
                    'renseignement' => 'renseignement',
                    'paiement' => 'paiement',
                    'reservation' => 'reservation',
                    'reclamation' => 'reclamation',
                ],
                'label' => 'Motif',
                'placeholder' => 'Sélectionner un motif',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invite::class,
        ]);
    }
}
