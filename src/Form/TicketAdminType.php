<?php

// src/Form/TicketAdminType.php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Statut;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champ pour l'email de l'utilisateur
            ->add('email', TextType::class, [
                'label' => 'Votre Email',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => "L'adresse Email est obligatoire."]),
                    new Email(['message' => "L'adresse Email n'est pas au bon format."])
                ],
            ])
            // Ajoute un champ pour la description du ticket
            ->add('description', TextareaType::class, [
                'label' => 'Description du ticket',
                'attr' => ['class' => 'form-control', 'rows' => 10, 'cols' => 50],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => "La description est obligatoire."]),
                    new Length([
                        'min' => 20,
                        'minMessage' => "La description doit contenir au moins {{ limit }} caractères.",
                        'max' => 250,
                        'maxMessage' => "La description ne doit pas dépasser {{ limit }} caractères.",
                    ]),
                ],
            ])
            // Ajoute un champ pour sélectionner la catégorie
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => "Veuillez sélectionner une catégorie"])
                ],
            ])
            // Ajoute un champ pour sélectionner le statut
            ->add('statut', EntityType::class, [
                'class' => Statut::class,
                'choice_label' => 'statut',
                'label' => 'Statut',
                'placeholder' => 'Choisir un statut',
                'required' => true,
            ])
            // Ajoute un champ pour sélectionner le responsable
            ->add('responsable', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'email',
                'label' => 'Responsable',
                'placeholder' => 'Choisir un responsable',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configure les options du formulaire, spécifiant la classe de données associée
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
