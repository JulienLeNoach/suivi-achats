<?php

namespace App\Form;


use App\Entity\CPV;
use App\Entity\Achat;
use App\Form\LibelleCpv;
use App\Form\UOAutocompleteField;
use App\Repository\CPVRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Form\FormationsAutocompleteField;
use App\Form\FournisseursAutocompleteField;
use App\Form\UtilisateursAutocompleteField;
use Proxies\__CG__\App\Entity\CPV as EntityCPV;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
        ->add('numero_achat', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => ['class' => 'fr-input'],
            'label_attr' => ['class' => 'fr-label'],
            'constraints' => [
                new Length(['max' => 4, 'maxMessage' => "Le numéro chrono doit contenir au maximum 4 caractères."]),
                new Regex([
                    'pattern' => '/^\d+$/', // Expression régulière pour autoriser uniquement les chiffres
                    'message' => 'Veuillez saisir uniquement des chiffres.', // Message en cas d'erreur
                ]),
            ],
        ])
        ->add('id_demande_achat', IntegerType::class, [
            'required' => false,
            'label' => false,
            'attr' => ['class' => 'fr-input'],
            'label_attr' => ['class' => 'fr-label'],

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

            ->add('date', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'choices' => $this->getYearChoices(),
                'placeholder' => 'Choisir une année',
                'data' => date('Y'),
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
                'label_attr' => ['class' => 'fr-label'],
                'constraints' => [
                    new Length(['max' => 5, 'maxMessage' => 'Le code postal doit contenir au maximum 5 caractères.']),
                ],

            ])
            ->add('utilisateurs', UtilisateursAutocompleteField::class, [  
                'required' => false,
                    'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('all_user', CheckboxType::class, [  
                'required' => false,
                'label'=>'Tous les util.',
                'mapped'=>false,
                'attr' => ['class' => 'all_user align-self-end'], 
                // 'label_attr' => ['class' => 'fr-label']
                'row_attr'=> ['class'=>'d-flex flex-column ']

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
                'label_attr' => ['class' => 'fr-label'],
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
                'label_attr' => ['class' => 'fr-label'],


            ])
            ->add('montant_achat_min', IntegerType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label'],
                'constraints' => [
                    new Length(['max' => 10, 'maxMessage' => 'Le montant achat minimum doit contenir au maximum 10 caractères.']),
                ],
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
                'label_attr' => ['class' => 'fr-label'],

            ])
            ->add('tax', ChoiceType::class, [
                'choices'  => [
                    'HT' => "ht",
                    'TTC' => "ttc",
                ],
                'required' => false,
                'expanded' => true,
                'label'=>false,
                'row_attr' => ['class' => 'radio-search d-flex justify-content-center'],
                'attr' => ['class' => ''], 
                'label_attr' => ['class' => 'fr-label'],
                'placeholder' => false,
                'mapped'=>false,
                'data'  => 'ht',
    
            ])
            ->add('sortField', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'hidden'], // Ajoutez une classe cachée si nécessaire
            ])
            
            ->add('sortDirection', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'hidden'],
            ])
            
            ->add('perPage', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'hidden'],
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
        'em' => $this->entityManager // Passer l'EntityManager aux options
    ]);
    $resolver->setAllowedTypes('allAchats', 'array');
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
