<?php

namespace App\DataFixtures;

use App\Entity\Travel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\Factory;

class TravelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i<=10; $i++) {
            $travel = new Travel();
            $travel->setNom($faker->sentence(3));
            $etat = $this->getReference('etat_'.rand(1,6));
            $travel->setEtat($etat);
            $travel->setDescription($faker->text(300));
            $travel->setMaxInscriptions($faker->numberBetween(1, 50));
            $travel->setRegistrationDeadLine($faker->dateTimeBetween('+1 month', '+2 month'));
            $travel->setStartDate($faker->dateTimeBetween('+2 months','+4 month'));
            $travel->setEndDate($faker->dateTimeBetween('+4 month', '+5 month'));
            $travel->setPrice($faker->NumberBetween(100, 10000));
            $travel->setPhoto($faker->image);
            $manager->persist($travel);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EtatFixtures::class,
        ];
    }
}
