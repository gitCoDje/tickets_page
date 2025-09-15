<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    // Route pour la page list qui affiche la liste de tous les tickets
    #[Route('/tickets', name: 'app_admin_list', methods: ['GET'])]
    public function list(TicketRepository $ticketRepository): Response
    {
        // Récupère tous les tickets depuis le repository de Ticket
        $tickets = $ticketRepository->findAll();

        // Affiche la liste des tickets dans la page list
        return $this->render('admin/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    // Nouvelle route pour afficher la liste des catégories
    #[Route('/categories', name: 'admin_category_list', methods: ['GET'])]
    public function listCategories(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();

        return $this->render('admin/categories_list.html.twig', [
            'categories' => $categories,
        ]);
    }
}
