<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TicketController extends AbstractController
{
    #[Route('/ticket', name: 'app_ticket', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager)
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


            $entityManager->persist($ticket);
            $entityManager->flush();
            return $this->redirectToRoute('app_ticket');
        }

        return $this->render('ticket/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
