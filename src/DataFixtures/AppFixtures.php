<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use App\Entity\Categorie;
use App\Entity\Utilisateur;
use App\Entity\Ticket;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un Admin 
        $admin = new Utilisateur();
        $admin->setEmail('admin@ticket.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'mdpadmin')
        );
        $manager->persist($admin);

        // Création des statuts
        $statutNouveau = new Statut();
        $statutNouveau->setStatut('Nouveau');
        $manager->persist($statutNouveau);

        // Création des catégories
        $categorieIncident = new Categorie();
        $categorieIncident->setNom('Anomalie');
        $manager->persist($categorieIncident);

        // Création d'un ticket exemple
        $ticket = new Ticket();
        $ticket->setAuteur('Marie Curie');
        $ticket->setEmail('marie.curiet@radium.com');
        $ticket->setDateOuverture(new \DateTime());
        $ticket->setDescription('Problème informatique');
        $ticket->setCategorie($categorieIncident);
        $ticket->setStatut($statutNouveau);
        $ticket->setResponsable($admin);

        $manager->persist($ticket);

        $manager->flush();
    }
}

