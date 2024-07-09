<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Travel;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TravelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('maxInscriptions',TextType::class,[
                'label'=>'Places disponibles'
            ])
            ->add('registrationDeadLine', null, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text'
            ])
            ->add('startDate', null, [
                'label' => 'Date de début',
                'widget' => 'single_text'
            ])
            ->add('endDate', null, [
                'label' => 'Date de fin',
                'widget' => 'single_text'
            ])
            ->add('price', TextType::class,[
                'label'=>'Prix (en Euro)',
            ])
            ->add('etat', EntityType::class, [

                'class' => Etat::class,
'choice_label' => 'libelle',
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
