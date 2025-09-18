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
        // Définition de l'email associé au ticket
        $ticket->setEmail('marie.curie@radium.com');
        // Définition de la date d'ouverture du ticket à la date actuelle
        $ticket->setDateOuverture(new \DateTime());
        // Définition de la description du problème
        $ticket->setDescription('Problème informatique');

        // Récupération de la catégorie "Incident" par référence
        $ticket->setCategorie($this->getReference('categorie-Incident', Categorie::class));
        // Récupération du statut "Nouveau" par référence
        $ticket->setStatut($this->getReference('statut-Nouveau', Statut::class));
        // Récupération de l'utilisateur responsable par référence
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
