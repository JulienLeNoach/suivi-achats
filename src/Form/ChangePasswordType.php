<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('code_service',TextType::class,[
            'disabled'=>true,
            'label'=>'Votre code service',
        ])
        ->add('nom_utilisateur',TextType::class,[
            'disabled'=>true,
            'label'=>'Votre nom',
        ])
        ->add('prenom_utilisateur',TextType::class,[
            'disabled'=>true,
            'label'=>'Votre prénom',
        ])
        ->add('old_password',PasswordType::class,[
            'label'=>'Votre mot de passe actuel',
            'mapped'=>false
        ])
        ->add('new_password', RepeatedType::class, ['type'=>PasswordType::class,
        'mapped'=>false,
        'invalid_message' => 'Les mots de passe ne correspondent pas',
        'label' => "Votre nouveau mot de passe",
        'required' => true,
        'first_options' => ['label' => 'Nouveau mot de passe'],
        'second_options' => ['label' => 'Confirmer votre nouveau mot de passe']])
        ->add('submit', SubmitType::class,['label' => "Modifier "]);

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
