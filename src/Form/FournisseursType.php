<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\Fournisseurs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FournisseursType extends AbstractType
{    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code_service', EntityType::class, ['label' => "Code service",
            'label' => 'Service',

            'class' => Services::class,
            'query_builder' => function (EntityRepository $er){
                    $user = $this->security->getUser();
                    return $er->createQueryBuilder('u')
                    ->andWhere('u.code_service = :val')
                    ->setParameter('val', $user->getCodeService()->getId());
                },
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label']])
            ->add('num_siret',IntegerType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'N° SIRET'])
            ->add('nom_fournisseur',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Nom du fournisseur'])
            ->add('ville',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Ville',
            'required'=>false])
            ->add('code_postal',IntegerType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Code postal',
            'required'=>false])
            ->add('pme', ChoiceType::class, [
                'choices'  => [
                    'Oui' => 1,
                    'Non' => 0
                ],
                'label' => "PME ?",
                'row_attr' => ['class' => 'radio-search'],
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label'],
                'required'=>false

            ])
            ->add('code_client',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Code client',
            'required'=>false])
            ->add('num_chorus_fournisseur',IntegerType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Numéro Chorus Fournisseur',
            'required'=>false])
            ->add('tel',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Téléphone',
            'required'=>false])
            ->add('FAX',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'FAX',
            'required'=>false])
            ->add('mail',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Mail',
            'required'=>false])
            ->add('etat_fournisseur',ChoiceType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Etat du fournisseur',
            'choices'  => [
                'Actif' => '1',
                'Inactif' => '0',
            ],])
            ->add('mobile',IntegerType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Mobile',
            'required'=>false]
        )
            

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseurs::class,
        ]);
    }
}
