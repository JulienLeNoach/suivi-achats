<?php

namespace App\Form;


use App\Entity\Achat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AchatSearchType extends AbstractType
{           

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('objet_achat', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('etat_achat', ChoiceType::class, [
                'choices'  => [
                    'En cours' => '0',
                    'Validé' => '2',
                    'Annulé' => '1'
                ],
                'required' => false,
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Etat de l'achat",
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
            ->add('date', TextType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false

            ])
            ->add('num_siret', FournisseursAutocompleteField::class, [  
                'required' => false,
                'label' => false,
            ])
            ->add('zipcode', TextType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false
            ])
            ->add('utilisateurs', UtilisateursAutocompleteField::class, [  
                'required' => false,
                'label' => false,
            ])
            ->add('code_uo', UOAutocompleteField::class, [  
                'required' => false,
                'label' => false
            ])
            ->add('code_cpv', LibelleCpv::class, [  
                'required' => false,
                'label' => false
            ])

            ->add('code_formation', FormationsAutocompleteField::class, [  
                'required' => false,
                'label' => false
            ])
            ->add('montant_achat_min', TextType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false
            ])
            ->add('montant_achat', TextType::class, [
                'required' => false,
                'label' => false
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn'
                ],
                'row_attr' => ['class' => 'sub-btn']
            ])
            ->setMethod('GET')

            // Récupération du formulaire
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
$resolver->setDefaults([
        'data_class' => Achat::class,
        'allAchats' => [],
        // 'cpv' => [],
    ]);
    $resolver->setAllowedTypes('allAchats', 'array');
    }
}
