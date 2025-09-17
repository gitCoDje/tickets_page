<?php

// tests/AdminControllerTest.php

namespace App\Tests\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testAdminPageAccessWithLogin()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $userRepository = $container->get('doctrine')->getRepository(Utilisateur::class);

        $testUser = $userRepository->findOneByEmail('admin@example.com');
        if (!$testUser) {
            $testUser = new Utilisateur();
            $testUser->setEmail('admin@example.com');
            $testUser->setRoles(['ROLE_ADMIN']);
            $testUser->setPassword('dummy_password');
            $em->persist($testUser);
            $em->flush();
        }

        $client->loginUser($testUser);

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue dans l’espace d’administration');

        $client->request('GET', '/tickets');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Listes des tickets');
    }
}
