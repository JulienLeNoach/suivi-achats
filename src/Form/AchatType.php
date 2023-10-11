<?php

namespace App\Form;

use App\Entity\TVA;
use App\Entity\Achat;
use App\Form\LibelleCpv;
use App\Form\UOAutocompleteField;
use App\Form\ServicesAutocompleteField;
use Symfony\Component\Form\AbstractType;
use App\Form\FormationsAutocompleteField;
use App\Form\FournisseursAutocompleteField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        
        $builder->add('num_siret', FournisseursAutocompleteField::class, [
            'required' => false,
            'label' => 'Fournisseur',

        ])
            ->add('code_service', ServicesAutocompleteField::class, [
                'required' => false,
                'label' => 'Service',

            ])

            ->add('code_formation', FormationsAutocompleteField::class, [
                'required' => false,
                'label' => 'Formation',

            ])
            ->add('utilisateurs', UtilisateursAutocompleteField::class, [
                'required' => false,
                'label' => 'Acheteur',

            ])
            ->add('date_sillage', DateType::class, [
                'required' => false,
                'label' => "Date d'enregistrement dans sillage",
                'widget' => 'single_text',

            ])
            ->add('date_commande_chorus', DateType::class, [
                'required' => false,
                'label' => 'Date de commande dans CF',
                'widget' => 'single_text',

            ])
            ->add('objet_achat', TextType::class, [
                'required' => false,
                'label' => "Objet de l'achat"
            ])
            ->add('type_marche', ChoiceType::class, [
                'choices'  => [
                    'MABC' => '0',
                    'MPPA' => '1'
                ],
                'required' => false,
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Type de marché",
                'row_attr' => ['class' => 'radio-search']
            ])
            ->add('montant_achat', TextType::class, [
                'required' => false,
                'label' => "Montant de l'achat"
            ])
            ->add('observations', TextType::class, [
                'required' => false,
                'label' => "Observations"
            ])

            ->add('place', ChoiceType::class, [
                'choices'  => [
                    'Non' => '0',
                    'Oui' => '1'
                ],
                'required' => false,
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
                'required' => false,
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Devis",
                'row_attr' => ['class' => 'radio-search']
            ])

            ->add('code_cpv', LibelleCpv::class, [
                'required' => false,
                'label' => 'CPV',

            ])
            ->add('tva_ident', EntityType::class,[
                'class' => TVA::class,
                'label' => 'TVA',
                'autocomplete' => true,

            ])

            ->add('code_uo', UOAutocompleteField::class, [
                'required' => false,
                'label' => 'Unité organique',

            ])
            ->add('return', SubmitType::class, [
                'label' => "Retour à la liste d'achats",
                'attr' => [
                    'class' => 'fr-btn search',
                    'onclick' => '', // Appelle la fonction JavaScript goBack() lors du clic
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
        ]);
    }
}
