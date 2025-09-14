<?php

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
    #[Route('/ticket', name: 'app_ticket', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, StatutRepository $statutRepository): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        
        dump($form->isSubmitted());

        if ($form->isSubmitted()) {
            dump($form->isValid());
            dump($form->getErrors(true, false));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateOuverture(new \DateTime());

            // Avoir le statut Nouveau à chaque nouveau ticket
            $statutNouveau = $statutRepository->findOneBy(['statut' => 'Nouveau']);
            $ticket->setStatut($statutNouveau);


            $entityManager->persist($ticket);
            $entityManager->flush();
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }
        return $this->render('ticket/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

        #[Route('/ticket/{id}', name: 'app_ticket_show', methods: ['GET'])]
        public function show(Ticket $ticket): Response{

            return $this->render('ticket/show.html.twig', [
                'ticket' => $ticket,
            ]);
        }

        
}
