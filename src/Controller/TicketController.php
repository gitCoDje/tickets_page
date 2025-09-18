<?php

// src/Controller/TicketController.php

namespace App\Controller;

// Import
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\StatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class TicketController extends AbstractController
{
    // Route pour la création d'un nouveau ticket
    #[Route('/ticket', name: 'app_ticket', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, StatutRepository $statutRepository): Response
    {
        // Création d'un nouvel objet Ticket
        $ticket = new Ticket();

        // Création d'un formulaire associé à la classe Ticket
        $form = $this->createForm(TicketType::class, $ticket);

        // Traite la requête en récupérant les données du formulaire
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Définition de la date d'ouverture du ticket à la date courante
            $ticket->setDateOuverture(new \DateTime());

            // Récupère le statut "Nouveau" pour assigner au ticket
            $statutNouveau = $statutRepository->findOneBy(['statut' => 'Nouveau']);
            
            $ticket->setStatut($statutNouveau);

            // Prépare la sauvegarde du ticket en base de données
            $entityManager->persist($ticket);
            // Exécute la sauvegarde en base
            $entityManager->flush();

            // Redirige vers la page de détail du ticket avec l'id du ticket actuel
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        // Affiche le formulaire de création du ticket dans la vue
        return $this->render('ticket/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    // Route pour afficher le détail d'un ticket via son id
    #[Route('/ticket/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        // Affiche le détail du ticket dans la vue dédiée
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
}