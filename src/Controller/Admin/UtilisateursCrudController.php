<?php

namespace App\Controller\Admin;

use App\Entity\Services;
use App\Entity\Utilisateurs;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\FormBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_OPT_UTILISATEURS')]
class UtilisateursCrudController extends AbstractCrudController
{


    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    
    public static function getEntityFqcn(): string
    {
        return Utilisateurs::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un utilisateur')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer un utilisateur')
            ->setPaginatorPageSize(20);
    }
    public function configureFields(string $pageName): iterable
    {
        // $services = $this->getDoctrine()->getRepository(Services::class)->findAll();

        if ($pageName === Crud::PAGE_INDEX) {

        return   [
            TextField::new('code_service')->setLabel('Service'),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
            BooleanField::new('administrateur_central'),

        ];
    }
    if ($pageName === Crud::PAGE_EDIT) {

        return   [
            BooleanField::new('etat_utilisateur'),
            AssociationField::new('code_service')
            ->setLabel('Service')
            ->autocomplete()
            ->setRequired(true)
            ->onlyOnForms(),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
            BooleanField::new('administrateur_central'),
            TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
        ];
    }        if ($pageName === Crud::PAGE_NEW) {

        return   [
            BooleanField::new('etat_utilisateur'),
            AssociationField::new('code_service')
            ->setLabel('Service')
            ->autocomplete()
            ->setRequired(true)
            ->onlyOnForms(),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
            BooleanField::new('administrateur_central'),
            TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
                'mapped' => false,
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms(),
            ChoiceField::new('roles')
                ->setLabel('Roles')
                ->setChoices([
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'OPT_Enironnement' => 'ROLE_OPT_CPV, ROLE_OPT_FORMATIONS, ROLE_OPT_FOURNISSEURS, ROLE_OPT_UO',
                    'OPT_Administration' => 'ROLE_OPT_UTILISATEURS, ROLE_OPT_SERVICES,ROLE_OPT_PARAMETRES, ROLE_OPT_DROITS',
                    'OPT_Achat' => 'ROLE_OPT_SAISIR_ACHATS , ROLE_OPT_RECHERCHE_ACHATS, ROLE_OPT_ANNULER_ACHATS, ROLE_OPT_MODIFIER_ACHATS, ROLE_OPT_REINT_ACHATS, ROLE_OPT_VALIDER_ACHATS',
                    'OPT_Statistique' => 'ROLE_OPT_ACTIV_ANNUEL, ROLE_OPT_CR_ANNUEL, ROLE_OPT_CUMUL_CPV, ROLE_OPT_DELAI_ANNUEL, ROLE_OPT_STAT_MPPA_MABC, ROLE_OPT_STAT_PME, ROLE_OPT_EXCTRACT_DONNEES',
                ])
                ->allowMultipleChoices()
                ->setRequired(true)
                ->onlyOnForms(),
        
            
        ];
    }
    }
    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $form->getData()->setPassword($hash);
        };
    }
}