<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Travel;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TravelRgistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('maxInscriptions')
            ->add('registrationDeadLine', null, [
                'widget' => 'single_text'
            ])
            ->add('startDate', null, [
                'widget' => 'single_text'
            ])
            ->add('endDate', null, [
                'widget' => 'single_text'
            ])
            ->add('price')
            ->add('photo')
            ->add('users', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Travel::class,
        ]);
    }
}
