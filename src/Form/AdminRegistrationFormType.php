<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AdminRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'utilisateur' => 'ROLE_USER',
                    'admin' => 'ROLE_ADMIN'
                
                ],
                // expanded => false & multiple => false : select
                // expanded => false & multiple => true : select multiple
                // expanded => true & multiple => false : radio buttons
                // expanded => true & multiple => true : checkboxes
                'expanded' => true,
                'multiple' => true
            ])
            ->add('password')
            ->add('username')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'mapped' => false,
                // mapped indique que Symfony ne va pas vérifier l'existence de ce champ dans l'entité User
                'attr' => ['autocomplete' => 'new-password']
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate'
            ],
            'data_class' => User::class,

        ]);
    }   
}