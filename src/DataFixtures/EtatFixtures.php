<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etat = [
            "Créé",
            "Ouvert",
            "Clôturé",
            "En cours",
            "Passé",
            "Annulé"
        ];

        foreach ($etat as $index => $libelle) {
            $etat = new Etat();
            $etat->setLibelle($libelle);
            $manager->persist($etat);
            $this->addReference('etat_' . ($index + 1), $etat); // Ajout de référence si nécessaire pour d'autres fixtures
        }

        $manager->flush();
    }
}
