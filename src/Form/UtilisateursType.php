<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UtilisateursType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data']->getId() !== null;

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
            ->add('nom_connexion',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Nom de connexion'])
            ->add('roles', CollectionType::class, [
                'attr' => ['class' => 'hidden'],
                'label_attr' => ['class' => 'hidden'],
                'data'=>["ROLE_USER"],
                // 'multiple' => true, // Permet la sélection multiple
                // 'expanded' => true, // Affiche les options sous forme de cases à cocher
            ])
            ->add('isAdmin', ChoiceType::class, [
                'label' => 'Fonctions administrateur',
                'required' => true, // Permet de décocher par défaut
                'attr' => ['class' => 'fr-input'], 
                'label_attr' => ['class' => 'fr-label'],
                'choices'  => [
                    'Inactif' => '0',
                    'Actif' => '1',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'fr-input'],
                    'label_attr' => ['class' => 'fr-label'] // Ajout de la classe fr-input
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['class' => 'fr-input'],
                    'label_attr' => ['class' => 'fr-label']
                     // Ajout de la classe fr-input
                ],
                'label'=>'Mot de passe',
                'required' => !$isEdit
            ])
            ->add('nom_utilisateur',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>"Nom de l'utilisateur"])
            ->add('prenom_utilisateur',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>"Prenom de l'utilisateur"])
            ->add('etat_utilisateur',ChoiceType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>"Etat de l'utilisateur",
            'choices'  => [
                'Actif' => '1',
                'Inactif' => '0',
            ],])
            ->add('trigram',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Trigram'])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
