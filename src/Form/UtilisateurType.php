<?php

// src/Form/UtilisateurType.php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champ pour l'email du responsable
            ->add('email', EmailType::class, [
                'label' => 'Email du responsable',
                'attr' => ['placeholder' => 'nom.responsable@ticket.com'],
                'required' => true,
            ])
            // Ajoute un champ pour sélectionner les rôles de l'utilisateur
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'required' => true,
                'multiple' => true, // Permet de sélectionner plusieurs rôles
            ])
            // Ajoute un champ pour le mot de passe
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => ['placeholder' => 'Entrez un mot de passe']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configure les options du formulaire, spécifiant la classe de données associée
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
