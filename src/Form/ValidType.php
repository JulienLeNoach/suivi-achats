<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ValidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Valider', SubmitType::class, [
            'attr' => [
                'class' => 'fr-btn'
            ],
            // 'row_attr' => ['class' => 'sub-btn']

        ])
        ->add('return', SubmitType::class, [
            'label' => "Retour à la liste d'achats",
            'attr' => [
                'class' => 'fr-btn',
                'onclick' => '', // Appelle la fonction JavaScript goBack() lors du clic
            ],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
