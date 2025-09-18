<?php

// src/Entity/Utilisateur.php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

// Indique que la classe Utilisateur est une entité Doctrine avec un repository associé
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    
    #[ORM\Id]    
    #[ORM\GeneratedValue]   
    #[ORM\Column]
    
    private ?int $id = null;

    // Définit la colonne pour l'email avec une longueur maximale de 180 caractères, unique
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    // Définit la colonne pour les rôles de l'utilisateur, stockée au format JSON
    #[ORM\Column(type: "json")]
    private array $roles = [];

    // Définit la colonne pour le mot de passe avec une longueur maximale de 255 caractères
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    public function getId(): ?int
    {
        // Retourne l'identifiant de l'utilisateur
        return $this->id;
    }

    public function getEmail(): ?string
    {
        // Retourne l'email de l'utilisateur
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        // Définit l'email de l'utilisateur
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        // Retourne l'identifiant unique de l'utilisateur, ici l'email
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        // Récupère les rôles de l'utilisateur et ajoute le rôle par défaut 'ROLE_USER'
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles); // Retourne les rôles uniques
    }

    public function setRoles(array $roles): static
    {
        // Définit les rôles de l'utilisateur
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        // Retourne le mot de passe de l'utilisateur
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        // Définit le mot de passe de l'utilisateur
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Méthode pour effacer les informations sensibles, si nécessaire
    }
}
