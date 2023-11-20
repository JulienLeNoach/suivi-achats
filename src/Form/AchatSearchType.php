<?php

namespace App\Form;


use App\Entity\Achat;
use App\Form\LibelleCpv;
use App\Form\UOAutocompleteField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Form\FormationsAutocompleteField;
use App\Form\FournisseursAutocompleteField;
use App\Form\UtilisateursAutocompleteField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;

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
        ->add('numero_achat', IntegerType::class, [
            'required' => false,
            'label' => false,
            'attr' => ['class' => 'fr-input'],
            'label_attr' => ['class' => 'fr-label']
        ])
        ->add('id_demande_achat', IntegerType::class, [
            'required' => false,
            'label' => false,
            'attr' => ['class' => 'fr-input'],
            'label_attr' => ['class' => 'fr-label']
        ])
            ->add('objet_achat', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('etat_achat', ChoiceType::class, [
                'choices'  => [
                    'En cours' => "EC",
                    'Validé' => "V",
                    'Annulé' => "A"
                ],
                'required' => false,
                'data' => "0", // Spécifiez ici la valeur par défaut en tant que chaîne "0"
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Etat de l'achat",
                'row_attr' => ['class' => 'radio-search'],
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('devis', ChoiceType::class, [
                'choices'  => [
                    'Prescripteur' => 'Pr',
                    'GSBdD/PFAF' => 'Gs'
                ],
                'required' => false,
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Devis",
                'row_attr' => ['class' => 'radio-search'],
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('type_marche', ChoiceType::class, [
                'choices'  => [
                    'MABC' => 'MABC',
                    'MPPA' => 'MPPA'
                ],
                'required' => false,
                'placeholder' => 'Tous',
                'expanded' => true,
                'label' => "Type de marché",
                'row_attr' => ['class' => 'radio-search'],
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('date', IntegerType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']


            ])
            ->add('num_siret', FournisseursAutocompleteField::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('zipcode', IntegerType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('utilisateurs', UtilisateursAutocompleteField::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('code_uo', UOAutocompleteField::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('code_cpv', LibelleCpv::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']

            ])

            ->add('code_formation', FormationsAutocompleteField::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('numero_ej', IntegerType::class, [  
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],  
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('montant_achat_min', IntegerType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('debut_rec', DateType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('fin_rec', DateType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])            
            ->add('montant_achat', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn '
                ],
                'row_attr' => ['class' => 'sub-btn d-flex mt-3'],
                'label' => 'Lancer la recherche', 

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
