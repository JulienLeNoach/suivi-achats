<?php

namespace App\Form;

use App\Entity\CPV;
use App\Entity\Services;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CPVType extends AbstractType
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

            ->add('code_cpv',IntegerType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Code CPV'])
            ->add('libelle_cpv',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Libellé CPV'])
            ->add('mt_cpv',IntegerType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Montant du CPV'])
            ->add('etat_cpv',ChoiceType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Etat du CPV',
            'choices'  => [
                'Actif' => '1',
                'Inactif' => '0',
            ],])
            ->add('mt_cpv_auto',IntegerType::class,['attr' => ['class' => 'hidden'], 
            'label_attr' => ['class' => 'hidden'],
            'data'=>'90000'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CPV::class,
        ]);
    }
}
