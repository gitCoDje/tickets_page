<?php

// src/DataFixtures/CategorieFixtures.php

namespace App\DataFixtures;


use App\Entity\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategorieFixtures extends Fixture
{
    // méthode load pour charger les données dans la base de données.
    public function load(ObjectManager $manager): void
    {
        // Création des catégories        
        $categories = ['Incident', 'Panne', 'Évolution', 'Anomalie', 'Information'];
        foreach ($categories as $nom) {
            $categorie = new Categorie();
            $categorie->setNom($nom);

            // prépare la sauvegarde en base
            $manager->persist($categorie);

            // Ajout d'une référence pour la réutiliser dans d'autres fixtures
            $this->addReference('categorie-' . $nom, $categorie);
        }

        // Execute la sauvegarde en base
        $manager->flush();
    }
}
