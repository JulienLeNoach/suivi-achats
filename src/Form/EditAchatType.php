<?php

namespace App\Form;

use App\Entity\CPV;
use App\Entity\TVA;
use App\Entity\Achat;
use App\Entity\Services;
use App\Form\LibelleCpv;
use App\Form\UOAutocompleteField;
use App\Repository\CPVRepository;
use Doctrine\ORM\EntityRepository;
use App\Form\ServicesAutocompleteField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Form\FormationsAutocompleteField;
use App\Form\FournisseursAutocompleteField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditAchatType extends AbstractType
{    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        
        $builder->add('num_siret', FournisseursAutocompleteField::class, [
            'required' => false,
            'label' => 'Fournisseur',

        ])
        ->add('code_service', EntityType::class, ['label' => "Code service",
        'label' => 'Service',

        'class' => Services::class,
            'attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label']])

            ->add('numero_achat', TextType::class, [
                'required' => false,
                'label' => 'N°Chrono',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('numero_marche', TextType::class, [
                'label' => 'Numero de marché',
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],                
                'required' => false,

            ])
            ->add('numero_ej_marche', TextType::class, [
                'label' => 'Numero EJ de marché',
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'required' => false,

            ])
            ->add('code_formation', FormationsAutocompleteField::class, [
                'required' => false,
                'label' => 'Formation',

            ])

            ->add('date_commande_chorus', DateType::class, [
                'required' => false,
                'label' => 'Date commande CF',
                'widget' => 'single_text',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('date_valid_inter', DateType::class, [
                'required' => false,
                'label' => 'Date de validation par le RUO',
                'widget' => 'single_text',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('objet_achat', TextType::class, [
                'required' => false,
                'label' => "Objet de l'achat",
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('type_marche', ChoiceType::class, [
                'choices'  => [
                    'MABC' => '0',
                    'MPPA' => '1'
                ],
                'required' => false,
                'placeholder' => false,
                'expanded' => true,
                'label' => "Type de marché",
                'row_attr' => ['class' => 'radio-search'],
                'label_attr' => ['class' => 'fr-label'],
                'attr' => ['class' => 'fr-input'], 

            ])
            ->add('montant_achat', NumberType::class, [
                'required' => true,
                'label' => "Montant de l'achat",
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('observations', TextareaType::class, [
                'required' => false,
                'label' => "Observations",
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])



            ->add('code_cpv', EntityType::class, [  
                'class' => CPV::class,
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'query_builder' => function (CPVRepository $cpvRepository) {
                    return $cpvRepository->createQueryBuilder('cpv');
                },
                'choice_label' => function (CPV $cpv) use ($options) {
                    $entityManager = $options['em'];
                    $achatRepository = $entityManager->getRepository(Achat::class);
                    $totalAchats = $achatRepository->getTotalAchatsForCPVByYear($cpv, date('Y'));
            
                    // Afficher le libellé du CPV avec le montant total des achats
                    return $cpv->getLibelleCpv() . ' - Total achats : ' . $totalAchats . ' €';
                },
                'choice_attr' => function (CPV $cpv) use ($options) {
                    $entityManager = $options['em'];
                    $achatRepository = $entityManager->getRepository(Achat::class);
                    $totalAchats = $achatRepository->getTotalAchatsForCPVByYear($cpv, date('Y'));
            
                    // Désactiver l'option si le montant total des achats dépasse le montant autorisé
                    if ($totalAchats > $cpv->getMtCpvAuto()) {
                        return ['disabled' => 'disabled', 'title' => 'Montant total des achats dépassé'];
                    }
            
                    return [];
                }
            ])
            ->add('tva_ident', EntityType::class,[
                'class' => TVA::class,
                'label' => 'TVA',
                'autocomplete' => true,
                'required' => true,
            ])

            ->add('code_uo', UOAutocompleteField::class, [
                'required' => false,
                'label' => 'Unité organique',

            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn'
                ],

            ])
;
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
}
