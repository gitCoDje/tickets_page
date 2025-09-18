<?php

// tests/AdminControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    // Test l'accès à la page admin avec un utilisateur connecté
    public function testAdminPageAccessWithLogin()
    {
        $client = static::createClient();
        $container = static::getContainer(); 
        $em = $container->get(EntityManagerInterface::class); 
        $userRepository = $container->get('doctrine')->getRepository(Utilisateur::class);

        // Vérifie si l'utilisateur de test existe, sinon le crée
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        if (!$testUser) {
            $testUser = new Utilisateur();
            $testUser->setEmail('admin@example.com');
            $testUser->setRoles(['ROLE_ADMIN']);
            $testUser->setPassword('test_password');
            $em->persist($testUser);
            $em->flush();
        }

        $client->loginUser($testUser); // Connecte l'utilisateur de test

        // Test l'accès à la page d'administration
        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Bienvenue dans l’espace d’administration');

        // Test l'accès à la page des tickets
        $client->request('GET', '/tickets');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Listes des tickets');
    }
}
