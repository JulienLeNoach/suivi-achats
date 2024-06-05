<?php

namespace App\Form;

use App\Entity\Achat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CRAnnuelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date', ChoiceType::class, [
            'required' => false,
            'label' => 'Année',
            'mapped' => false,
            'attr' => ['class' => 'fr-input'],
            'label_attr' => ['class' => 'fr-label'],
            'choices' => $this->getYearChoices(),
            'placeholder' => 'Choisir une année',
            'data' => date('Y'),
            'row_attr' => ['class' => 'me-3'],

        ])        
        ->add('jourcalendar', ChoiceType::class, [
            'choices'  => [
                'Jours ouvrés' => "jO",
                'Tout les jours' => "tJ",
            ],
            'required' => false,
            'expanded' => true,
            'label'=>"Calcul des délais : ",
            'row_attr' => ['class' => ''],
            'attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'placeholder' => false,
            'mapped'=>false,
            'data'  => 'tJ',

        ])
        ->add('tax', ChoiceType::class, [
            'choices'  => [
                'HT' => "ht",
                'TTC' => "ttc",
            ],
            'required' => false,
            'expanded' => true,
            'label'=>false,
            'row_attr' => ['class' => 'hidden'],
            'attr' => ['class' => ''], 
            'label_attr' => ['class' => 'fr-label'],
            'placeholder' => false,
            'mapped'=>false,
            'data'  => 'ht',

        ])
        ->add('excel', SubmitType::class, [
            'attr' => [
                'class' => 'fr-btn '
            ],
            'label' => 'Export Excel', 
            'row_attr' => ['class' => 'p-3'],

        ])

            ->add('numero_achat', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('id_demande_achat', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('date_sillage', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('date_commande_chorus', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('date_validation', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('date_notification', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('date_annulation', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('numero_ej', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('objet_achat', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('type_marche', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('montant_achat', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('observations', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('etat_achat', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])

            ->add('utilisateurs', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('code_cpv', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('num_siret', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('code_service', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('code_formation', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
            ->add('code_uo', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'hidden'],  
            ])
        ;
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
