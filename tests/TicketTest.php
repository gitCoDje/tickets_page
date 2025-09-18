<?php

namespace App\Tests;

use App\Entity\Categorie;
use App\Entity\Statut;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    // Test des getters et setters pour la description
    public function testDescriptionGetterSetter(): void
    {
        $ticket = new Ticket();
        $description = "Problème technique constaté";

        $ticket->setDescription($description);
        $this->assertEquals($description, $ticket->getDescription(), "La description doit être conforme");
    }

    // Test des getters et setters pour l'email
    public function testEmailGetterSetter(): void
    {
        $ticket = new Ticket();
        $email = "client@example.com";

        $ticket->setEmail($email);
        $this->assertEquals($email, $ticket->getEmail(), "L'email doit être conforme");
    }

    // Test des getters et setters pour la catégorie
    public function testCategorieGetterSetter(): void
    {
        $ticket = new Ticket();
        $categorie = new Categorie();
        $categorie->setNom('Technique');

        $ticket->setCategorie($categorie);
        $this->assertSame($categorie, $ticket->getCategorie(), "La catégorie doit être conforme");
    }

    // Test des getters et setters pour le statut
    public function testStatutGetterSetter(): void
    {
        $ticket = new Ticket();
        $statut = new Statut();
        $statut->setStatut('Ouvert');

        $ticket->setStatut($statut);
        $this->assertSame($statut, $ticket->getStatut(), "Le statut doit être conforme");
    }

    // Test des getters et setters pour le responsable
    public function testResponsableGetterSetter(): void
    {
        $ticket = new Ticket();
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('user@example.com');

        $ticket->setResponsable($utilisateur);
        $this->assertSame($utilisateur, $ticket->getResponsable(), "Le responsable doit être conforme");
    }

    // Test pour vérifier que la date de clôture est mise à jour selon le statut
    public function testSetStatutUpdatesDateCloture(): void
    {
        $ticket = new Ticket();

        $statutOuvert = new Statut();
        $statutOuvert->setStatut('Ouvert');

        $statutFermé = new Statut();
        $statutFermé->setStatut('Fermé');

        // Quand le statut est ouvert, la date de clôture doit être nulle
        $ticket->setStatut($statutOuvert);
        $this->assertNull($ticket->getDateCloture(), "La date de clôture doit être nulle si statut est autre que 'Résolu' ou 'Fermé'");

        // Quand le statut est fermé, la date de clôture doit être définie
        $ticket->setStatut($statutFermé);
        $this->assertInstanceOf(\DateTime::class, $ticket->getDateCloture(), "La date de clôture doit être un objet DateTime");

        $now = new \DateTime();
        $diffSeconds = $now->getTimestamp() - $ticket->getDateCloture()->getTimestamp();
        $this->assertLessThan(5, abs($diffSeconds), "La date de clôture doit être proche du moment actuel");
    }
}
