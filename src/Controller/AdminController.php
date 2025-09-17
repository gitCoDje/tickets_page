<?php

// src/Controller/AdminController.php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Statut;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Form\CategorieType;
use App\Form\StatutType;
use App\Form\TicketAdminType;
use App\Form\TicketStatusType;
use App\Form\UtilisateurType;
use App\Repository\CategorieRepository;
use App\Repository\StatutRepository;
use App\Repository\TicketRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    /* ========== GESTION DES TICKETS ========== */

    // Route pour la page list qui affiche la liste de tous les tickets
    #[Route('/tickets', name: 'app_admin_ticket_list', methods: ['GET'])]
    public function list(TicketRepository $ticketRepository): Response
    {
        // Récupère tous les tickets depuis le repository de Ticket
        $tickets = $ticketRepository->findAll();

        // Affiche la liste des tickets dans la page list
        return $this->render('admin/ticket_list.html.twig', [
            'tickets' => $tickets,
        ]);
    }
    // Route pour modifer les tickets depuis la liste
    #[Route('/admin/ticket/{id}/edit', name: 'app_admin_ticket_edit', methods: ['GET', 'POST'])]
    public function editTicket(Request $request, Ticket $ticket, EntityManagerInterface $em): Response
    {
        // Vérification rôle : admin ou responsable
        if ($this->isGranted('ROLE_ADMIN')) {
            // Donne à l'admin l'accès au modificationdu ticket complet
            $form = $this->createForm(TicketAdminType::class, $ticket);
        } elseif ($this->isGranted('ROLE_USER')) {
            // Donne au personnel l'accès seulement au statut du ticket
            $form = $this->createForm(TicketStatusType::class, $ticket);
        } else {
            throw $this->createAccessDeniedException('Accès interdit');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // récupère le statut qui à été cliqué   
            $statut = $ticket->getStatut();
            if ($statut) {
                // Défini l'utilisateur connecté via getUser comme responsable dés que le statut a été définit   
                $ticket->setResponsable($this->getUser());
            }

            $em->flush();
            $this->addFlash('success', 'Ticket modifié avec succès.');

            return $this->redirectToRoute('app_admin_ticket_list');
        }
        return $this->render('admin/ticket_edit.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
        ]);
    }
    // Route pour supprimer un ticket existant
    #[Route('/admin/ticket/{id}/delete', name: 'app_admin_ticket_delete', methods: ['GET'])]
    public function deleteTicket(int $id, TicketRepository $ticketRepository, EntityManagerInterface $em): Response
    {
        $ticket = $ticketRepository->find($id);

        if (!$ticket) {
            throw $this->createNotFoundException('Ticket non trouvé.');
        }

        $em->remove($ticket);
        $em->flush();

        $this->addFlash('success', 'Ticket supprimé avec succès.');

        return $this->redirectToRoute('app_admin_ticket_list'); // ou autre route liste tickets
    }


    /* ========== GESTION CATEGORIE ========== */

    // Route pour afficher la liste des catégories
    #[Route('/categories', name: 'app_admin_category_list', methods: ['GET'])]
    public function listCategories(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();

        return $this->render('admin/category_list.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Route pour la création d'une nouvelle catégorie
    #[Route('/categories/create', name: 'app_admin_category_create', methods: ['GET', 'POST'])]
    public function createCategory(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer une nouvelle catégorie',
        ]);
    }

    // Route pour modifier une catégorie existante
    #[Route('/categories/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(int $id, Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier la catégorie',
        ]);
    }
    // Route pour supprimer une catégorie existante
    #[Route('/categories/{id}/delete', name: 'app_admin_category_delete', methods: ['GET'])]
    public function deleteCategory(int $id, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');

        return $this->redirectToRoute('app_admin_category_list');
    }

    /* ========== GESTION STATUT ========== */

    // Route pour afficher la liste des statuts
    #[Route('/statuts', name: 'app_admin_status_list', methods: ['GET'])]
    public function listStatuts(StatutRepository $statutRepository): Response
    {
        $statuts = $statutRepository->findAll();

        return $this->render('admin/status_list.html.twig', [
            'statuts' => $statuts,
        ]);
    }

    // Route pour la création d'un nouveau statut
    #[Route('/statuts/create', name: 'app_admin_status_create', methods: ['GET', 'POST'])]
    public function createStatus(Request $request, EntityManagerInterface $em): Response
    {
        $statut = new Statut();
        $form = $this->createForm(StatutType::class, $statut);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($statut);
            $em->flush();

            $this->addFlash('success', 'Statut créée avec succès.');
            return $this->redirectToRoute('app_admin_status_list');
        }

        return $this->render('admin/status_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer une nouvelle statut',
        ]);
    }

    // Route pour modifier un statut existant
    #[Route('/statuses/{id}/edit', name: 'app_admin_status_edit', methods: ['GET', 'POST'])]
    public function editNomStatus(int $id, Request $request, StatutRepository $statutRepository, EntityManagerInterface $em): Response
    {
        $statut = $statutRepository->find($id);

        if (!$statut) {
            throw $this->createNotFoundException('Statut non trouvée.');
        }

        $form = $this->createForm(StatutType::class, $statut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Statut modifiée avec succès.');
            return $this->redirectToRoute('app_admin_status_list');
        }

        return $this->render('admin/status_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier le statut',
        ]);
    }
    // Route pour supprimer une statut existante
    #[Route('/statuses/{id}/delete', name: 'app_admin_status_delete', methods: ['GET'])]
    public function deleteNomStatus(int $id, StatutRepository $statutRepository, EntityManagerInterface $em): Response
    {
        $statut = $statutRepository->find($id);

        if (!$statut) {
            throw $this->createNotFoundException('Statut non trouvée.');
        }

        $em->remove($statut);
        $em->flush();

        $this->addFlash('success', 'Statut supprimée avec succès.');

        return $this->redirectToRoute('app_admin_status_list');
    }


    /* ========== GESTION UTILISATEUR ========== */

    // Route pour afficher la liste des utilisateurs
    #[Route('/utilisateurs', name: 'app_admin_user_list', methods: ['GET'])]
    public function listUtilisateurs(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findAll();
        return $this->render('admin/user_list.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
    // Inject UserPasswordHasherInterface via constructeur
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    // Route pour la création d'un nouvel utilisateur
    #[Route('/utilisateurs/create', name: 'app_admin_user_create', methods: ['GET', 'POST'])]
    public function createUtilisateur(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère le mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Hash le mot de passe suivant la méthode vue dans la fixture
                $encodedPassword = $this->passwordHasher->hashPassword($utilisateur, $plainPassword);
                $utilisateur->setPassword($encodedPassword);
            }

            $em->persist($utilisateur);
            $em->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('app_admin_user_list');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouvel utilisateur',
        ]);
    }

    // Route pour la modification d'un utilisateur existant
    #[Route('/utilisateurs/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUtilisateur(int $id, Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_admin_user_list');
        }

        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier l\'utilisateur',
        ]);
    }

    // Route pour la suppression d'un utilisateur existant
    #[Route('/utilisateurs/{id}/delete', name: 'app_admin_user_delete', methods: ['GET'])]
    public function deleteUtilisateur(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Empêche la suppression de l'admin principal
        if ($utilisateur->getEmail() === 'admin@ticket.com') {
            $this->addFlash('error', 'Impossible de supprimer l\'administrateur principal.');
            return $this->redirectToRoute('app_admin_user_list');
        }

        $em->remove($utilisateur);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('app_admin_user_list');
    }
}
