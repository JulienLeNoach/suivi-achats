<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ValidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn',
                    'data-valid-achat-target' => 'submitButton',
                ],
            ])
            ->add('val', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de validation',
                'constraints' => [new NotBlank(['message' => 'Ce champ est requis.'])],
                'required' => true,
                'attr' => [
                    'class' => 'fr-input ml-5',
                ],
                'row_attr' => [
                    'class' => ' p-1',
                ],
            ])
            ->add('not', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de notification',
                'constraints' => [new NotBlank(['message' => 'Ce champ est requis.'])],
                'required' => true,
                'attr' => [
                    'class' => 'fr-input',
                ],
                'row_attr' => [
                    'class' => ' p-1',
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
