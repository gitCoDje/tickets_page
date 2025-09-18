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
        // Tableau des noms de catégories à créer     
        $categories = ['Incident', 'Panne', 'Évolution', 'Anomalie', 'Information'];
        // Parcours chaque nom de catégorie pour créer une entité correspondante
        foreach ($categories as $nom) {
            $categorie = new Categorie();
            // Définit le nom de la catégorie
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
