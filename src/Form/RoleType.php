<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_connexion', EntityType::class, [
            'class' => Utilisateurs::class,
            'required' => false,
            'label' => "Nom d'utilisateur",
            // 'autocomplete' => true,
            'attr' => ['data-action' => 'change->role#getRoles',
            'class' => 'fr-input '],
                'label_attr' => ['class' => 'fr-label'],
                    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
