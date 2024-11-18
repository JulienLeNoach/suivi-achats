<?php

namespace App\Form;

use App\Entity\GSBDD;
use App\Entity\Services;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GSBDDType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code_service', EntityType::class, [
                'label' => 'Service',
                'class' => Services::class,
                'query_builder' => function (EntityRepository $er) {
                    $user = $this->security->getUser();
                    return $er->createQueryBuilder('s')
                        ->andWhere('s.id = :val')
                        ->setParameter('val', $user->getCodeService()->getId());
                },
                'attr' => ['class' => 'fr-input hidden'],
                'label_attr' => ['class' => 'fr-label hidden']
            ])
            ->add('libelle_gsbdd', TextType::class, [
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'label' => 'Libellé GSBDD'
            ])
            ->add('etat_gsbdd', ChoiceType::class, [
                'attr' => ['class' => 'fr-input'],
                'label_attr' => ['class' => 'fr-label'],
                'label' => 'État du GSBDD',
                'choices' => [
                    'Actif' => true,
                    'Inactif' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GSBDD::class,
        ]);
    }
}
