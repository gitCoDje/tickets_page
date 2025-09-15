<?php

// src/Controller/TicketController.php

namespace App\Controller;

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

        // Création d'un formulaire associés à la class Ticket
        $form = $this->createForm(TicketType::class, $ticket);

        // Traite la requête en récupérant les données du formulaire
        $form->handleRequest($request);        

        // Verifie si le formulaire est soumis et s'il est conforme
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateOuverture(new \DateTime());

            // Récupère le statut "Nouveau" à chaque nouveau ticket
            $statutNouveau = $statutRepository->findOneBy(['statut' => 'Nouveau']);
            $ticket->setStatut($statutNouveau);

            // prépare la sauvegarde en base
            $entityManager->persist($ticket);
            // Execute la sauvegarde
            $entityManager->flush();
            // redirige vers la page show (detail du ticket) avec l'id du ticket actuel
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }
        // Affiche le formulaire dans la page ticket
        return $this->render('ticket/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    // Route pour la page show qui detaille le ticket
    #[Route('/ticket/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {        
        // Affiche le détail du ticket dans la page show
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
    
    // Route pour la page list qui affiche la liste de tous les tickets
    #[Route('/tickets', name: 'app_ticket_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupère tous les tickets depuis le repository de Ticket
        $tickets = $entityManager->getRepository(Ticket::class)->findAll();

        // Affiche la liste des tickets dans la page list
        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }

}
