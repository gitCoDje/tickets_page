<?php

// src/DataFixtures/UtilisateurFixtures.php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
    // constructeur injecte le service UserPasswordHasherInterface
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    // méthode load pour charger les données dans la base de données.
    public function load(ObjectManager $manager): void
    {
        // Création d'un Admin avec rôle ROLE_ADMIN
        $admin = new Utilisateur();
        $admin->setEmail('admin@ticket.com');
        $admin->setRoles(['ROLE_ADMIN']);
        // Sécurisation du mot de passe par Hashage
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'adminmdp')
        );
        // prépare la sauvegarde en base
        $manager->persist($admin);        

        // Création d'un personnel avec rôle ROLE_USER
        $personnel = new Utilisateur();
        $personnel->setEmail('personnel@ticket.com');
        $personnel->setRoles(['ROLE_USER']);
        $personnel->setPassword($this->passwordHasher->hashPassword($personnel, 'adminmdp'));
        $manager->persist($personnel);
        
        // Ajout d'une référence pour la réutiliser dans d'autres fixtures
        $this->addReference('utilisateur-admin', $admin);
        $this->addReference('utilisateur-personnel', $personnel);

        // Execute la sauvegardeen base
        $manager->flush();        
    }
}
