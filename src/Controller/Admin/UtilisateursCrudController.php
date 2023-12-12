<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
        if ($pageName === Crud::PAGE_INDEX) {

        return   [
            TextField::new('code_service')->setLabel('Service'),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
        ];
    }
    if ($pageName === Crud::PAGE_EDIT) {

        return   [
            BooleanField::new('etat_utilisateur'),
            TextField::new('code_service')->setLabel('Service'),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
        ];
    }        if ($pageName === Crud::PAGE_NEW) {

        return   [
            BooleanField::new('etat_utilisateur'),
            TextField::new('code_service')->setLabel('Service'),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
        ];
    }
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
       
    }
    
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }
    
    private function encodePassword($entity)
    {
        // dd($entity);
        if ($entity instanceof Utilisateurs && !empty($entity->getPassword())) {
            $entity->setPassword(
                $this->userPasswordHasher->hashPassword($entity, $entity->getPassword())
            );
        }
    }
}