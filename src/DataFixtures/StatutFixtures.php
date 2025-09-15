<?php

// src/DataFixtures/StatutFixtures.php

namespace App\DataFixtures;


use App\Entity\Statut;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class StatutFixtures extends Fixture
{
    // méthode load pour charger les données dans la base de données.
    public function load(ObjectManager $manager): void
    {
        // Création des statuts  
        $statuts = ['Nouveau', 'Ouvert', 'Résolu', 'Fermé'];
        foreach ($statuts as $libelle) {
            $statut = new Statut();
            $statut->setStatut($libelle);

            // prépare la sauvegarde en base
            $manager->persist($statut);

            // Ajout d'une référence pour la réutiliser dans d'autres fixtures
            $this->addReference('statut-' . $libelle, $statut);
        }

        // Execute la sauvegarde en base
        $manager->flush();
    }
}
