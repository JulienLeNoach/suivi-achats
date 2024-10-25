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
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Ajout pour les champs de délai

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
            // Ajout des champs de délai
            ->add('delai_transmissions', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'label' => 'Délai pour Transmissions (en jours)',
                'empty_data' => 5, // Valeur par défaut
                'data' => 5, // Valeur par défaut
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne peut pas être vide']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Le nombre doit être compris entre {{ min }} et {{ max }}',
                    ]),
                ],

            ])
            ->add('delai_traitement', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'label' => 'Délai pour Traitement (en jours)',
                'empty_data' => 3, // Valeur par défaut
                'data' => 3, // Valeur par défaut
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne peut pas être vide']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Le nombre doit être compris entre {{ min }} et {{ max }}',
                    ]),
                ],

            ])
            ->add('delai_notifications', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'label' => 'Délai pour Notifications (en jours)',
                'empty_data' => 5, // Valeur par défaut
                'data' => 5, // Valeur par défaut
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne peut pas être vide']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Le nombre doit être compris entre {{ min }} et {{ max }}',
                    ]),
                ],

            ])
            ->add('delai_total', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'label' => 'Délai total (en jours)',
                'empty_data' => 15, // Valeur par défaut
                'data' => 15, // Valeur par défaut
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne peut pas être vide']),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Le nombre doit être compris entre {{ min }} et {{ max }}',
                    ]),
                ],

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
                'row_attr' => ['class' => 'fr-input-stat'],
                
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
            ->add('etat_achat', ChoiceType::class, [
                'choices'  => [
                    'Achats validé seulement' => "valid",
                    'Tout les achats' => "all",
                ],
                'expanded' => true,
                'label'=>false,
                'row_attr' => ['class' => 'radio-search'],
                'attr' => ['class' => ''], 
                'label_attr' => ['class' => 'fr-label'],
                'placeholder' => false,
                'mapped'=>false,
                'data'  => 'valid',
            ])
            ->add('annee_precedente', ChoiceType::class, [
                'choices'  => [
                    'Année en cours seulement' => "anneeEnCours",
                    'Année en cours et année précédente' => "anneePrecedente",
                ],
                'expanded' => true,
                'label'=>false,
                'row_attr' => ['class' => 'radio-search'],
                'attr' => ['class' => ''], 
                'label_attr' => ['class' => 'fr-label'],
                'placeholder' => false,
                'mapped'=>false,
                'data'  => 'anneeEnCours',
            ])
            ->add('montant_achat_min', IntegerType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label'],
                'empty_data' => '0'

            ])
            ->add('montant_achat', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn search'
                ],
                'row_attr' => ['class' => 'sub-btn d-flex mt-3'],
                'label' => 'Lancer la recherche',
            ]);
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
