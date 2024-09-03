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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
        $today = new \DateTime();
        $todayFormatted = $today->format('Y-m-d');

        $builder
            // ->add('date_sillage', DateType::class, [
            //     'required' => true,
            //     'widget' => 'single_text',
            //     'label' => false,
            //     'attr' => [
            //         'class' => 'fr-input',
            //         'max' => $todayFormatted,
            //         'data-add-achat-target' => 'dateSillage'
            //     ],
            //     'label_attr' => ['class' => 'fr-label']
            // ])
            ->add('date_commande_chorus', DateType::class, [
                'required' => true,
                'label' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input',
                    'max' => $todayFormatted,
                    'data-add-achat-target' => 'dateCommandeChorus'
                ],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('date_valid_inter', DateType::class, [
                'required' => true,
                'label' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'fr-input',
                    'max' => $todayFormatted,
                    'data-add-achat-target' => 'dateValidInter'
                ],
                'label_attr' => ['class' => 'fr-label']
            ])
            // Ajoutez le reste des champs ici
            ->add('Valider', SubmitType::class, [
                'attr' => ['class' => 'fr-btn']
            ])
            ->add('objet_achat', TextType::class, [
                'required' => true,
                'label' => false,
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
            ->add('id_demande_achat', IntegerType::class, [
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('type_marche', ChoiceType::class, [
                'choices' => [
                    'MABC' => '0',
                    'MPPA' => '1'
                ],
                'placeholder' => false,
                'expanded' => true,
                'label' => "Type de marché",
                'row_attr' => ['class' => 'radio-search mt-5'],
                'label_attr' => ['class' => 'fr-label'],
                'attr' => ['class' => 'fr-input'],
                'required' => true
            ])
            ->add('montant_achat', NumberType::class, [
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
            ->add('code_cpv', LibelleCpv::class, [
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('num_siret', FournisseursAutocompleteField::class, [
                'label' => "Fournisseur",
                'label' => false,
                'required' => true,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('code_service', EntityType::class, [
                'label' => "Code service",
                'label' => false,
                'class' => Services::class,
                'query_builder' => function (EntityRepository $er) {
                    $user = $this->security->getUser();
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.code_service = :val')
                        ->setParameter('val', $user->getCodeService()->getId());
                },
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('code_formation', FormationsAutocompleteField::class, [
                'required' => true,
                'label' => false,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('code_uo', UOAutocompleteField::class, [
                'label' => "Unité organique",
                'label' => false,
                'required' => true,
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label']
            ])
            ->add('tva_ident', EntityType::class, [
                'class' => TVA::class,
                'label' => false,
                // 'autocomplete' => true,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.tva_etat = :etat')
                        ->setParameter('etat', 1);
                },

                'attr' => ['class' => 'fr-input', 'data-add-achat-target' => 'tvaIdent'],
                'label_attr' => ['class' => 'fr-label'],
                'placeholder' => 'Sélectionnez une option',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une option.',
                    ]),
                ]
            ])
            
            
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'fr-btn mb-5 me-3'
                ],
            ])
            ->add('Fermer', ButtonType::class, [
                'attr' => [
                    'class' => 'fr-btn ms-3',
                    'onclick' => "window.location.href='" . $options['close_path'] . "'"
                ],
                'label' => 'Fermer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
            'attr' => ['class' => 'fr-form'],
            'close_path' => '/search'
        ]);

        $resolver->setAllowedTypes('close_path', 'string');
    }
}