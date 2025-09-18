<?php

// src/Controller/AdminController.php

namespace App\Controller;

// Importation des entités
use App\Entity\Categorie;
use App\Entity\Statut;
use App\Entity\Ticket;
use App\Entity\Utilisateur;

// Importation des types de formulaire
use App\Form\CategorieType;
use App\Form\StatutType;
use App\Form\TicketAdminType;
use App\Form\TicketStatusType;
use App\Form\UtilisateurType;

// Importation des repositories
use App\Repository\CategorieRepository;
use App\Repository\StatutRepository;
use App\Repository\TicketRepository;
use App\Repository\UtilisateurRepository;

// Importation du gestionnaire d'entités Doctrine
use Doctrine\ORM\EntityManagerInterface;
// Contrôleur abstrait Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Pour gérer la requête HTTP
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// Pour hash le mot de passe utilisateur
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// Pour définir les routes
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    // Route vers la page d'accueil admin
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Affiche la page d'accueil et indique le nom du contrôleur
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /* ========== GESTION DES TICKETS ========== */

    // Route qui affiche la liste de tous les tickets existants
    #[Route('/tickets', name: 'app_admin_ticket_list', methods: ['GET'])]
    public function list(TicketRepository $ticketRepository): Response
    {
        // Récupère tous les tickets de la base
        $tickets = $ticketRepository->findAll();

        // Affiche la liste dans la vue correspondante
        return $this->render('admin/ticket_list.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    // Route pour modifier un ticket depuis la liste admin
    #[Route('/admin/ticket/{id}/edit', name: 'app_admin_ticket_edit', methods: ['GET', 'POST'])]
    public function editTicket(Request $request, Ticket $ticket, EntityManagerInterface $em): Response
    {
        // Vérifie le rôle de l'utilisateur connecté
        if ($this->isGranted('ROLE_ADMIN')) {
            // Le form admin pour modification complète
            $form = $this->createForm(TicketAdminType::class, $ticket);
        } elseif ($this->isGranted('ROLE_USER')) {
            // Le form personnel pour modification du statut uniquement
            $form = $this->createForm(TicketStatusType::class, $ticket);
        } else {
            // Accès interdit à tout autre rôle
            throw $this->createAccessDeniedException('Accès interdit');
        }

        // Traite les données soumises du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on met à jour le ticket
        if ($form->isSubmitted() && $form->isValid()) {
            $statut = $ticket->getStatut();
            if ($statut) {
                // Si le statut du ticket est défini, indique l'utilisateur comme responsable
                $ticket->setResponsable($this->getUser());
            }

            $em->flush();
            // Affiche une notification de succès
            $this->addFlash('success', 'Ticket modifié avec succès.');

            // Redirige vers la liste des tickets
            return $this->redirectToRoute('app_admin_ticket_list');
        }

        // Affiche le formulaire de modification
        return $this->render('admin/ticket_edit.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
        ]);
    }

    // Route pour supprimer un ticket existant
    #[Route('/admin/ticket/{id}/delete', name: 'app_admin_ticket_delete', methods: ['GET'])]
    public function deleteTicket(int $id, TicketRepository $ticketRepository, EntityManagerInterface $em): Response
    {
        // Recherche le ticket par ID
        $ticket = $ticketRepository->find($id);

        if (!$ticket) {
            // Si aucun ticket trouvé, erreur 404
            throw $this->createNotFoundException('Ticket non trouvé.');
        }

        // Supprime le ticket
        $em->remove($ticket);
        $em->flush();

        // Affiche une notification de succès
        $this->addFlash('success', 'Ticket supprimé avec succès.');

        // Redirige vers la liste des tickets admin
        return $this->redirectToRoute('app_admin_ticket_list');
    }

    /* ========== GESTION CATEGORIE ========== */

    // Route pour afficher toutes les catégories
    #[Route('/categories', name: 'app_admin_category_list', methods: ['GET'])]
    public function listCategories(CategorieRepository $categorieRepository): Response
    {
        // Récupère la liste des catégories
        $categories = $categorieRepository->findAll();

        // Affiche la liste dans la vue admin
        return $this->render('admin/category_list.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Route pour créer une nouvelle catégorie
    #[Route('/categories/create', name: 'app_admin_category_create', methods: ['GET', 'POST'])]
    public function createCategory(Request $request, EntityManagerInterface $em): Response
    {
        // Instancie une nouvelle catégorie
        $categorie = new Categorie();
        // Crée le formulaire de création
        $form = $this->createForm(CategorieType::class, $categorie);

        // Traite le formulaire
        $form->handleRequest($request);

        // Enregistre la catégorie si soumission valide
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            // Affiche notification et redirige vers la liste
            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        // Affiche le formulaire de création
        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer une nouvelle catégorie',
        ]);
    }

    // Route pour modifier une catégorie existante
    #[Route('/categories/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(int $id, Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        // Recherche la catégorie par ID
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            // Si aucune catégorie trouvée, erreur 404
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        // Crée le formulaire de modification
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        // Modifie la catégorie si soumission valide
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // Notification et redirection
            $this->addFlash('success', 'Catégorie modifiée avec succès.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        // Affiche le formulaire de modification
        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier la catégorie',
        ]);
    }

    // Route pour supprimer une catégorie existante
    #[Route('/categories/{id}/delete', name: 'app_admin_category_delete', methods: ['GET'])]
    public function deleteCategory(int $id, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response
    {
        // Recherche la catégorie
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        // Suppression de la catégorie
        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');

        // Redirige vers la liste des catégories
        return $this->redirectToRoute('app_admin_category_list');
    }

    /* ========== GESTION STATUT ========== */

    // Route pour afficher tous les statuts
    #[Route('/statuts', name: 'app_admin_status_list', methods: ['GET'])]
    public function listStatuts(StatutRepository $statutRepository): Response
    {
        $statuts = $statutRepository->findAll();

        return $this->render('admin/status_list.html.twig', [
            'statuts' => $statuts,
        ]);
    }

    // Route pour créer un nouveau statut
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

    // Injecte le service de hash du mot de passe par le constructeur
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    // Route pour créer un nouvel utilisateur
    #[Route('/utilisateurs/create', name: 'app_admin_user_create', methods: ['GET', 'POST'])]
    public function createUtilisateur(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Prend le mot de passe en clair du form
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Hash le mot de passe avant de l'enregistrer
                $encodedPassword = $this->passwordHasher->hashPassword($utilisateur, $plainPassword);
                $utilisateur->setPassword($encodedPassword);
            }

            $em->persist($utilisateur);
            $em->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('app_admin_user_list');
        }

        // Affiche le formulaire de création
        return $this->render('admin/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouvel utilisateur',
        ]);
    }

    // Route pour modifier un utilisateur existant
    #[Route('/utilisateurs/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUtilisateur(int $id, Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        // Modifie l'utilisateur si le formulaire est valide
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

    // Route pour supprimer un utilisateur existant
    #[Route('/utilisateurs/{id}/delete', name: 'app_admin_user_delete', methods: ['GET'])]
    public function deleteUtilisateur(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Vérifie si c'est l'admin principal pour protéger de la suppression
        if ($utilisateur->getEmail() === 'admin@ticket.com') {
            $this->addFlash('error', 'Impossible de supprimer l\'administrateur principal.');
            return $this->redirectToRoute('app_admin_user_list');
        }

        // Supprime l'utilisateur
        $em->remove($utilisateur);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('app_admin_user_list');
    }
}
