<?php

namespace App\Form;

use App\Entity\Services;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ServicesType extends AbstractType
{
    private $security;

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
            ->add('nom_service',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>"Nom du service"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Services::class,
        ]);
    }
}
