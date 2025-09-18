<?php

// src/Entity/Statut.php

namespace App\Entity;

use App\Repository\StatutRepository;
use Doctrine\ORM\Mapping as ORM;

// Indique que la classe Statut est une entité Doctrine avec un repository associé
#[ORM\Entity(repositoryClass: StatutRepository::class)]
class Statut
{
    // Indique que cette propriété est la clé primaire
    #[ORM\Id]
    // Indique que l'identifiant est généré automatiquement par la base de données
    #[ORM\GeneratedValue]
    // Définit cette propriété comme une colonne dans la base de données
    #[ORM\Column]
    private ?int $id = null;

    // Définit la colonne pour le statut avec une longueur maximale de 20 caractères
    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    public function getId(): ?int
    {
        // Retourne l'identifiant du statut
        return $this->id;
    }

    public function getStatut(): ?string
    {
        // Retourne le nom du statut
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        // Définit le nom du statut
        $this->statut = $statut;

        // Permet le chaînage des méthodes
        return $this;
    }
}
