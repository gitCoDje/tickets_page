<?php

// src/Controller/SecurityController.php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// Pour les outils d'authentification utilisateur
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // Route qui gère la page de connexion
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l’erreur d’authentification s’il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d’utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affiche la vue du formulaire de connexion avec les données précédentes
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Route qui gère la déconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode reste vide car le mécanisme de déconnexion est géré par le firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
