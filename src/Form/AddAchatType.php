<?php

namespace App\Form;

use App\Entity\Achat;
use App\Entity\TVA;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddAchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('date_sillage',DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => false,
                        
            ])
            ->add('date_commande_chorus',DateType::class, [
                'required' => true,
                'label' => false,
                'widget' => 'single_text',

            ])
            ->add('objet_achat', TextType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('type_marche', ChoiceType::class, [
                'choices'  => [
                    'MABC' => '0',
                    'MPPA' => '1'
                ],
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Type de marché",
                'row_attr' => ['class' => 'radio-search']
            ])
            ->add('montant_achat', TextType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('observations', TextType::class, [
                'required' => false,
                'label' => false,
            ])

            ->add('place', ChoiceType::class, [
                'choices'  => [
                    'Non' => '0',
                    'Oui' => '1'
                ],
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Marché avec publicité ?",
                'row_attr' => ['class' => 'radio-search']
            ])
            ->add('devis', ChoiceType::class, [
                'choices'  => [
                    'Prescripteur' => '0',
                    'GSBdD/PFAF' => '1'
                ],
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Devis",
                'row_attr' => ['class' => 'radio-search']
            ])
            
            ->add('code_cpv', LibelleCpv::class,[
                'label' => "CPV",
                'label' => false,
                'required' => true,

            ])

            ->add('num_siret', FournisseursAutocompleteField::class,[
                'label' => "Fournisseur",
                'label' => false,
                'required' => true,

            ])
            ->add('code_service', ServicesAutocompleteField::class,[
                'label' => "Service",
                'label' => false,
                'required' => true,
            ])

            ->add('code_formation', FormationsAutocompleteField::class
            ,[
                'label' => "Formation",
                'label' => false,
                'required' => true,
            ])
                
            ->add('code_uo', UOAutocompleteField::class,[
                'label' => "Unité organique",
                'label' => false,
                'required' => true,
            ])
                            
            ->add('tva_ident', EntityType::class,[
                'class' => TVA::class,
                'label' => false,
                'autocomplete' => true,
                'required' => true,

            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn'
                ],
                'row_attr' => ['class' => 'sub-btn']

            ])
            ->add('return', SubmitType::class, [
                'label' => "Retour à la liste d'achats",
                'attr' => [
                    'class' => 'fr-btn search',
                    'onclick' => '', // Appelle la fonction JavaScript goBack() lors du clic
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class    ,
        ]);
    }
}
