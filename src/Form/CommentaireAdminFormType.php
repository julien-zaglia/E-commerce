<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu')
            ->add('auteur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username' // menu déroulant 
            ])
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'Nom'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
