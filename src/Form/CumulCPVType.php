<?php

namespace App\Form;

use App\Entity\CPV;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CumulCPVType extends AbstractType
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
        ])
        ->add('alertValue', IntegerType::class, [
            'required' => false,
            'label' => "Valeur d'alerte",
            'mapped' => false,
            'attr' => ['class' => 'fr-input'],  
            'label_attr' => ['class' => 'fr-label'],
            
            'data'=>'40000'
        ])
        ->add('recherche', SubmitType::class, [
            'attr' => [
                'class' => 'fr-btn search'
            ],
            'row_attr' => ['class' => 'sub-btn d-flex p-3'],
            'label' => 'Lancer la recherche', 
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CPV::class,
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
