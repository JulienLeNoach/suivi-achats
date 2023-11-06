<?php

namespace App\Form;

use App\Entity\Achat;
use App\Form\LibelleCpv;
use App\Form\UOAutocompleteField;
use Symfony\Component\Form\AbstractType;
use App\Form\FormationsAutocompleteField;
use App\Form\FournisseursAutocompleteField;
use App\Form\UtilisateursAutocompleteField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StatisticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('date', TextType::class, [
            'required' => false,
            'label' => "Année",
            'mapped' => false,
            'empty_data'  => date('Y'),


        ])
        ->add('num_siret', FournisseursAutocompleteField::class, [  
            'required' => false,
        ])

        ->add('utilisateurs', UtilisateursAutocompleteField::class, [  
            'required' => false,
        ])
        ->add('code_uo', UOAutocompleteField::class, [  
            'required' => false,
        ])
        ->add('code_cpv', LibelleCpv::class, [  
            'required' => false,
        ])

        ->add('code_formation', FormationsAutocompleteField::class, [  
            'label' => 'Formation',
            'required' => false,
        ])
        ->add('tax', ChoiceType::class, [
            'label' => 'Taxe',
            'choices' => [
                'HT' => 'ht',
                'TTC' => 'ttc',
            ],
            'mapped' => false
        ])
        ->add('recherche', SubmitType::class, [
            'attr' => [
                'class' => 'fr-btn search'
            ]

        ])
        ->add('graph', SubmitType::class, [
            'label' => 'Graph',
            'attr' => [
                'class' => 'fr-btn search hidden test'
            ]
        ])
        ->add('print', SubmitType::class, [
            'label' => 'Imprimer',
            'attr' => [
                'class' => 'fr-btn search hidden'   
            ]
        ])
        ->add('excel', SubmitType::class, [
            'label' => 'Exporter vers Excel',
            'attr' => [
                'class' => 'fr-btn search hidden'
            ]
        ])
        // Récupération du formulaire
        ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
        ]);
    }
}
