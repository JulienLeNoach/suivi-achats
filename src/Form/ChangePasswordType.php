<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ChangePasswordType extends AbstractType
{
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
            ->add('nom_connexion',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Nom de connexion'])
            ->add('roles', CollectionType::class, [
                'attr' => ['class' => 'hidden'],
                'label_attr' => ['class' => 'hidden'],
                // 'data'=>["ROLE_USER"],
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
                'invalid_message' => 'Les champs du nouveau mot de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'mapped'=>false
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
