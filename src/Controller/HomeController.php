<?php

// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function accueil(): Response
    {
        return $this->render('home/index.html.twig', [
            'titre' => 'Bienvenue sur le suivi des tickets',
        ]);
    }
}
