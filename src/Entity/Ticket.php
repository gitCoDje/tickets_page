<?php

// src/Entity/Ticket.php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Indique que la classe Ticket est une entité Doctrine avec un repository associé
#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    // Indique que cette propriété est la clé primaire
    #[ORM\Id]
    // Indique que l'identifiant est généré automatiquement par la base de données
    #[ORM\GeneratedValue]
    // Définit cette propriété comme une colonne dans la base de données
    #[ORM\Column]
    private ?int $id = null;

    // Définit la colonne pour l'email avec une longueur maximale de 255 caractères, nullable
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    // Définit la colonne pour la date d'ouverture du ticket
    #[ORM\Column]
    private ?\DateTime $dateOuverture = null;

    // Définit la colonne pour la date de clôture du ticket, nullable
    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCloture = null;

    // Définit la colonne pour la description du ticket avec un type TEXT
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // Définit une relation ManyToOne avec la classe Categorie
    #[ORM\ManyToOne(targetEntity: Categorie::class)]
    private ?Categorie $categorie = null;

    // Définit une relation ManyToOne avec la classe Statut
    #[ORM\ManyToOne(targetEntity: Statut::class)]
    private ?Statut $statut = null;

    // Définit une relation ManyToOne avec la classe Utilisateur
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    private ?Utilisateur $responsable = null;

    public function getId(): ?int
    {
        // Retourne l'identifiant du ticket
        return $this->id;
    }

    public function getEmail(): ?string
    {
        // Retourne l'email associé au ticket
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        // Définit l'email associé au ticket
        $this->email = $email;

        return $this;
    }

    public function getDateOuverture(): ?\DateTime
    {
        // Retourne la date d'ouverture du ticket
        return $this->dateOuverture;
    }

    public function setDateOuverture(\DateTime $dateOuverture): static
    {
        // Définit la date d'ouverture du ticket
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    public function getDateCloture(): ?\DateTime
    {
        // Retourne la date de clôture du ticket
        return $this->dateCloture;
    }

    public function setDateCloture(?\DateTime $dateCloture): static
    {
        // Définit la date de clôture du ticket
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getDescription(): ?string
    {
        // Retourne la description du ticket
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        // Définit la description du ticket
        $this->description = $description;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        // Retourne la catégorie associée au ticket
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        // Définit la catégorie associée au ticket
        $this->categorie = $categorie;
        return $this;
    }

    public function getStatut(): ?Statut
    {
        // Retourne le statut associé au ticket
        return $this->statut;
    }

    public function setStatut(?Statut $statut): static
    {
        // Définit le statut associé au ticket
        $this->statut = $statut;

        // Met à jour la date de clôture si le statut est "Résolu" ou "Fermé"
        if ($statut && in_array($statut->getStatut(), ['Résolu', 'Fermé'])) {
            $this->dateCloture = new \DateTime();
        } else {
            $this->dateCloture = null; // Optionnel : effacer la date si statut différent
        }

        return $this;
    }
    public function getResponsable(): ?Utilisateur
    {
        // Retourne l'utilisateur responsable du ticket
        return $this->responsable;
    }

    public function setResponsable(?Utilisateur $responsable): static
    {
        // Définit l'utilisateur responsable du ticket
        $this->responsable = $responsable;
        return $this;
    }
}
