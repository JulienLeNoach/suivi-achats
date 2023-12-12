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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StatisticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('date', ChoiceType::class, [
            'required' => false,
            'mapped' => false,
            'attr' => ['class' => 'fr-input'],
            'label_attr' => ['class' => 'fr-label'],
            'choices' => $this->getYearChoices(),
            'placeholder' => 'Choisir une année',
            'label'=>'Année',
            'data' =>  date('Y'),
            // 'row_attr' => ['class' => 'fr-input-stat']

        ])
        ->add('jourcalendar', ChoiceType::class, [
            'choices'  => [
                'Jours ouvrés' => "jO",
                'Tout les jours' => "tJ",
            ],
            'required' => false,
            'expanded' => true,
            'label'=>false,
            'row_attr' => ['class' => 'radio-search'],
            'attr' => ['class' => ''], 
            'label_attr' => ['class' => 'fr-label'],
            'placeholder' => false,
            'mapped'=>false,
            'data'  => 'tJ',

        ])
        ->add('num_siret', FournisseursAutocompleteField::class, [  
            'required' => false,
            'label' => "N° SIRET",
            'attr' => ['class' => 'fr-input'],  
            'label_attr' => ['class' => 'fr-label'],
            'row_attr' => ['class' => 'fr-input-stat']

        ])

        ->add('utilisateurs', UtilisateursAutocompleteField::class, [  
            'required' => false,
            'label' => "Utilisateur",
            'attr' => ['class' => 'fr-input'],  
            'label_attr' => ['class' => 'fr-label'],
            'row_attr' => ['class' => 'fr-input-stat']
        ])
        ->add('code_uo', UOAutocompleteField::class, [  
            'required' => false,
            'label' => "Unité organique",
            'attr' => ['class' => 'fr-input'],  
            'label_attr' => ['class' => 'fr-label'],
            'row_attr' => ['class' => 'fr-input-stat']

        ])
        ->add('code_cpv', LibelleCpv::class, [  
            'required' => false,
            'label' => "CPV",
            'attr' => ['class' => 'fr-input'],  
            'label_attr' => ['class' => 'fr-label'],
            'row_attr' => ['class' => 'fr-input-stat']
        ])

        ->add('code_formation', FormationsAutocompleteField::class, [  
            'label' => 'Formation',
            'required' => false,
            'attr' => ['class' => 'fr-input'],  
            'label_attr' => ['class' => 'fr-label'],
            'row_attr' => ['class' => 'fr-input-stat']

        ])
        ->add('tax', ChoiceType::class, [
            'choices'  => [
                'HT' => "ht",
                'TTC' => "ttc",
            ],
            'required' => false,
            'expanded' => true,
            'label'=>false,
            'row_attr' => ['class' => 'radio-search'],
            'attr' => ['class' => ''], 
            'label_attr' => ['class' => 'fr-label'],
            'placeholder' => false,
            'mapped'=>false,
            'data'  => 'ht',

        ])
        ->add('recherche', SubmitType::class, [
            'attr' => [
                'class' => 'fr-btn search'
            ],
            'row_attr' => ['class' => 'sub-btn d-flex mt-3'],
            'label' => 'Lancer la recherche',

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

    private function getYearChoices()
    {
        $currentYear = date('Y');
        $endYear = $currentYear - 20; // par exemple, 10 ans en arrière
        $years = [];
    
        for ($year = $currentYear; $year >= $endYear; $year--) {
            $years[$year] = $year;
        }
    
        return $years;
    }
}
