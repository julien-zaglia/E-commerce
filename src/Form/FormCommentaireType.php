<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormCommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu')
            ->add('username', EntityType::class, [
                'class' => User::class,
                // nous allons choisir dans un menu déroulant un auteur parmi la liste des utilisateurs existants
                'choice_label' => 'username'
            ])
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                  // nous allons choisir dans un menu déroulant un article parmi la liste des articles existants
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
