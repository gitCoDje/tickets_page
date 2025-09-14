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
        $statuts = ['Nouveau','Ouvert','Résolu','Fermé'];
        foreach($statuts as $libelle) {
        $statut = new Statut();
        $statut->setStatut($libelle);
        $manager->persist($statut);}

        // Création des catégories        
        $categories = ['Incident','Panne','Évolution','Anomalie','Information'];
        foreach($categories as $nom) {
        $categorie = new Categorie();
        $categorie->setNom($nom);
        $manager->persist($categorie);}

        // Création d'un ticket exemple
        $ticket = new Ticket();
        $ticket->setEmail('marie.curie@radium.com');
        $ticket->setDateOuverture(new \DateTime());
        $ticket->setDescription('Problème informatique');
        $ticket->setCategorie($categorie);
        $ticket->setStatut($statut);
        $ticket->setResponsable($admin);

        $manager->persist($ticket);

        $manager->flush();
    }
}

