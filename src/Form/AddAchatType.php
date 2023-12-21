<?php

namespace App\Form;

use App\Entity\TVA;
use App\Entity\Achat;
use App\Entity\Services;
use App\Form\LibelleCpv;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Form\FournisseursAutocompleteField;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddAchatType extends AbstractType
{

    private $security;

public function __construct(Security $security)
{
    $this->security = $security;
}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('date_sillage',DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
                        
            ])
            ->add('date_commande_chorus',DateType::class, [
                'required' => true,
                'label' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('date_valid_inter',DateType::class, [
                'required' => true,
                'label' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('objet_achat', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('id_demande_achat', IntegerType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('type_marche', ChoiceType::class, [
                'choices'  => [
                    'MABC' => '0',
                    'MPPA' => '1'
                ],
                'placeholder' => false,
                'expanded' => true,
                'label' => "Type de marché",
                'row_attr' => ['class' => 'radio-search mt-5'],
                'label_attr' => ['class' => 'fr-label'],
                'attr' => ['class' => 'fr-input'],

            ])
            ->add('montant_achat', IntegerType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('observations', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])

            ->add('place', ChoiceType::class, [
                'choices'  => [
                    'Non' => '0',
                    'Oui' => '1'
                ],
                'placeholder' => false,
                'expanded' => true,
                'label' => "Marché avec publicité ?",
                'row_attr' => ['class' => 'radio-search'],
                'label_attr' => ['class' => 'fr-label'],
                'attr' => ['class' => 'fr-input'],


            ])
            ->add('devis', ChoiceType::class, [
                'choices'  => [
                    'Prescripteur' => '0',
                    'GSBdD/PFAF' => '1'
                ],
                'placeholder' => false,
                'expanded' => true,
                'label' => "Devis",
                'row_attr' => ['class' => 'radio-search mt-5'],
                'label_attr' => ['class' => 'fr-label'],
                'attr' => ['class' => 'fr-input'],

            ])
            
            ->add('code_cpv', LibelleCpv::class,[
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
                
            ])

            ->add('num_siret', FournisseursAutocompleteField::class,[
                'label' => "Fournisseur",
                'label' => false,
                'required' => true,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

            ])
            ->add('code_service', EntityType::class, ['label' => "Code service",
            'label' => false,

            'class' => Services::class,
            'query_builder' => function (EntityRepository $er){
                    $user = $this->security->getUser();
                    return $er->createQueryBuilder('u')
                    ->andWhere('u.code_service = :val')
                    ->setParameter('val', $user->getCodeService()->getId());
                },
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']])

            ->add('code_formation', FormationsAutocompleteField::class
            ,[
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']


            ])
                
            ->add('code_uo', UOAutocompleteField::class,[
                'label' => "Unité organique",
                'label' => false,
                'required' => true,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']
            ])
                            
            ->add('tva_ident', EntityType::class,[
                'class' => TVA::class,
                'label' => false,
                'autocomplete' => true,
                'required' => true,
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']

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
            'data_class' => Achat::class    ,
        ]);
    }
}
