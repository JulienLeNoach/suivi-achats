<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $isEdit = $options['data']->getId() !== null; // Vérifie si le formulaire correspond à l'édition ou la création d'un utilisateur en vérifiant si son ID existe déjà

        $builder
        ->add('code_service', EntityType::class, ['label' => "Code service",
            'label' => 'Service',

            'class' => Services::class,
            'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('u');

                },
                'attr' => ['class' => 'fr-input '], 
                'label_attr' => ['class' => 'fr-label ']])
            ->add('nom_connexion',TextType::class,['attr' => ['class' => 'fr-input'], 
            'label_attr' => ['class' => 'fr-label'],
            'label'=>'Nom de connexion'])
            ->add('roles', CollectionType::class, [
                'attr' => ['class' => 'hidden'],
                'label_attr' => ['class' => 'hidden'],

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
                'options' => ['attr' => ['class' => 'password-field fr-input']],
                'required' => !$isEdit,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => 'Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.'
                        ])
                    ]
                ],
                'second_options' => ['label' => 'Répéter le mot de passe'],
                'mapped' => !$isEdit
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
