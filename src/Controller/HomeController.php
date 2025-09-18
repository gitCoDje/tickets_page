<?php

// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Pour gérer les réponses HTTP
use Symfony\Component\HttpFoundation\Response;
// Pour la déclaration de routes avec annotation
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // Route qui pointe vers la racine du site ('/')
    #[Route('/', name: 'app_home')]
    public function homeRedirect(): Response
    {
        // Redirige automatiquement vers la route de création de ticket
        return $this->redirectToRoute('app_ticket');
    }
}
