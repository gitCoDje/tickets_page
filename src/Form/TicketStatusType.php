<?php

// src/Form/TicketStatusType.php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Statut;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajoute un champ 'statut' lié à l'entité Statut
            ->add('statut', EntityType::class, [
                // Peuple le select
                'class' => Statut::class,
                // affiche les options pour permettre de selectionner le statut                
                'choice_label' => 'statut',
                // Affiche le statut actuel du label
                'label' => 'Statut',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Relie le formulaire à l'entité Ticket pour récupérer les données du ticket
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
