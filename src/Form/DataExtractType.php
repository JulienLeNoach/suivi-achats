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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DataExtractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        /*---------------------- Partie conditionnelle du formulaire de recherche -------------------------*/
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
                'row_attr' => ['class' => 'me-3'],
    
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
                'label_attr' => ['class' => 'fr-label'],
                'empty_data' => '0'

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
        /*---------------------- Partie choix des attribut à exporter -------------------------*/

            ->add('chrono_attr', CheckboxType::class, [
                'label'    => 'N° Chrono',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('code_service_attr', CheckboxType::class, [
                'label'    => 'Code service',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('nom_service_attr', CheckboxType::class, [
                'label'    => 'Nom service',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('code_acheteur_attr', CheckboxType::class, [
                'label'    => 'Code acheteur',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('nom_acheteur_attr', CheckboxType::class, [
                'label'    => 'Nom acheteur',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('code_formation_attr', CheckboxType::class, [
                'label'    => 'Code formation',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('libelle_formation_attr', CheckboxType::class, [
                'label'    => 'Libellé formation',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('siret_fournisseur_attr', CheckboxType::class, [
                'label'    => 'SIRET Fournisseur',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('nom_fournisseur_attr', CheckboxType::class, [
                'label'    => 'Nom fournisseur',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('code_uo_attr', CheckboxType::class, [
                'label'    => 'Code UO',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('libelle_uo_attr', CheckboxType::class, [
                'label'    => 'Libellé UO',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('code_cpv_attr', CheckboxType::class, [
                'label'    => 'Code CPV',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('libelle_cpv_attr', CheckboxType::class, [
                'label'    => 'Libellé CPV',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('id_dem_achat_attr', CheckboxType::class, [
                'label'    => 'Ident. demande achat',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('date_sillage_attr', CheckboxType::class, [
                'label'    => 'date SILLAGE',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('date_commande_chorus_attr', CheckboxType::class, [
                'label'    => 'Date commande CF',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('date_valid_inter_attr', CheckboxType::class, [
                'label'    => 'Date validation RUO',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('date_valid_attr', CheckboxType::class, [
                'label'    => 'Date validation',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('date_notif_attr', CheckboxType::class, [
                'label'    => 'Date notification',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('date_annul_attr', CheckboxType::class, [
                'label'    => 'Date annulation',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('ej_attr', CheckboxType::class, [
                'label'    => 'N° EJ',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('objet_achat_attr', CheckboxType::class, [
                'label'    => "Objet de l'achat",
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('type_marche_attr', CheckboxType::class, [
                'label'    => 'Type du marché',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('montant_ht_attr', CheckboxType::class, [
                'label'    => 'Montant HT',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('tva_attr', CheckboxType::class, [
                'label'    => 'TVA',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])
            ->add('montant_ttc_attr', CheckboxType::class, [
                'label'    => 'Montant TTC',
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])

            ->add('obs_attr', CheckboxType::class, [
                'label'    => 'Observations',
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'required' => false,
                'data' => true,
            ])
            ->add('etat_achat_attr', CheckboxType::class, [
                'label'    => "Etat de l'achat",
                'required' => false,
                'attr' => ['class' => 'ms-3'],
                'row_attr' => ['class' => 'p-3'],
                'mapped' => false,
                'data' => true,
            ])

            // ->add('ville_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'Ville Fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'],
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('cp_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'CP Fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'], 
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('pme_fournisseurs_attr', CheckboxType::class, [
            //     'label'    => 'PME (O/N) ?',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'], 
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('code_client_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'Code client fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'],
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('chorus_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'N° Chorus fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'],
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('tel_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'Tel. fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'],
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('fax_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'Fax fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'],
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            // ->add('mail_fournisseur_attr', CheckboxType::class, [
            //     'label'    => 'Mail fournisseur',
            //     'required' => false,
            //     'attr' => ['class' => 'ms-3'],
            //     'row_attr' => ['class' => 'p-3'],
            //     'mapped' => false,
            //     'data' => true,
            // ])
            ->add('excel', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn '
                ],
                'label' => 'Export Excel', 
                'row_attr' => ['class' => 'p-3'],

            ])
            ->setMethod('GET')

            // Récupération du formulaire
            ->getForm();
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
