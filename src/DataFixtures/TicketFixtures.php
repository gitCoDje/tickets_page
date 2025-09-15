<?php

// src/DataFixtures/TicketFixtures.php

namespace App\DataFixtures;


use App\Entity\Ticket;
use App\Entity\Categorie;
use App\Entity\Statut;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    // méthode load pour charger les données dans la base de données.
    public function load(ObjectManager $manager): void
    {
        // Création d'un ticket exemple
        $ticket = new Ticket();
        $ticket->setEmail('marie.curie@radium.com');
        $ticket->setDateOuverture(new \DateTime());
        $ticket->setDescription('Problème informatique');

    $ticket->setCategorie($this->getReference('categorie-Incident', Categorie::class));
    $ticket->setStatut($this->getReference('statut-Nouveau', Statut::class));
    $ticket->setResponsable($this->getReference('utilisateur-admin', Utilisateur::class));

        // prépare la sauvegarde en base
        $manager->persist($ticket);
        // Exécute la sauvegarde en base
        $manager->flush();
    }

    // Retourne les dépendances nécessaires pour le chargement des fixtures.
    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
            StatutFixtures::class,
            CategorieFixtures::class,
        ];
    }
}
