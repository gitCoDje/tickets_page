<?php

// src/Form/TicketType.php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Statut;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute le champ email
            ->add('email', TextType::class, [
                'label' => 'Votre Email',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "L'adresse Email est obligatoire."
                    ]),
                    new Email([
                        'message' => "L'adresse Email n'est pas au bon format."
                    ]),
                ]
            ])
            // Ajoute le champ description
            ->add('description', TextareaType::class, [
                'label' => 'Description du ticket',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'height: 100px; width: 100%;',
                    'minlength'=> 20,
                    'maxlength'=> 250
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "La description est obligatoire."
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => "La description doit contenir au moins {{ limit }} caractères.",
                        'max' => 250,
                        'maxMessage' => "La description ne doit pas dépasser {{ limit }} caractères."
                    ]),
                ]
            ])
            // Ajoute l'option catégrorie
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => "Veuillez sélectionner une catégorie"])
                ]                
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
