<?php

namespace App\Form;

use App\Entity\Parametres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('four1', IntegerType::class, [  
                'required' => true,
                'label'=>'Valeur n°1',
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label'],
                'row_attr' => ['class' => 'p-3'],

            ])
            ->add('four2', IntegerType::class, [  
                'required' => true,
                'label'=>'Valeur n°2',
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label'],
                'row_attr' => ['class' => 'p-3'],
            ])
            ->add('four3', IntegerType::class, [  
                'required' => true,
                'label'=>'Valeur n°3',
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label'],
                'row_attr' => ['class' => 'p-3'],


            ])
            ->add('four4', IntegerType::class, [  
                'required' => true,
                'label'=>'Valeur n°4',
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label'],
                'row_attr' => ['class' => 'p-3'],
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn me-5'
                ],
                
            ])
            ->add('return', SubmitType::class, [
                'label' => "Retour à la liste d'achats",
                'attr' => [
                    'class' => 'fr-btn',
                    'onclick' => 'history.back();return false;'
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parametres::class,
        ]);
    }
}
