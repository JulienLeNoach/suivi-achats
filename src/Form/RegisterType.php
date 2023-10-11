<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_utilisateur', TextType::class, ['label' => "Nom de l'utilisateur",
            ])
            ->add('prenom_utilisateur', TextType::class, ['label' => "Prénom de l'utilisateur",
            ])               
            ->add('code_service', EntityType::class, ['label' => "Code service",
            'class' => Services::class])
            ->add('nom_connexion', TextType::class, ['label' => "Nom de connexion",
            ])
            ->add('password', RepeatedType::class, ['type'=>PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'label' => "Votre mot de passe",
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer votre mot de passe'],
                ])
            ->add('submit', SubmitType::class,['label' => "S'inscrire"] )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
