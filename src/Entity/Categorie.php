<?php

// src/Entity/Categorie.php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;

// Indique que la classe Categorie est une entité Doctrine avec un repository associé
#[ORM\Entity(repositoryClass: CategorieRepository::class)] 
class Categorie
{
    // Indique que cette propriété est la clé primaire
    #[ORM\Id] 
    // Indique que l'identifiant est généré automatiquement par la base de données
    #[ORM\GeneratedValue] 
    // Définit cette propriété comme une colonne dans la base de données
    #[ORM\Column] 
    private ?int $id = null;

    // Définit la colonne pour le nom avec une longueur maximale de 50 caractères
    #[ORM\Column(length: 50)] 
    private ?string $nom = null;

    public function getId(): ?int
    {
        // Retourne l'identifiant de la catégorie
        return $this->id; 
    }

    public function getNom(): ?string
    {
        // Retourne le nom de la catégorie
        return $this->nom; 
    }

    public function setNom(string $nom): static
    {
        // Définit le nom de la catégorie
        $this->nom = $nom; 

        // Permet le chaînage des méthodes
        return $this; 
    }
}
