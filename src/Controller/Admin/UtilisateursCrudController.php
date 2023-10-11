<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateurs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_OPT_UTILISATEURS')]
class UtilisateursCrudController extends AbstractCrudController
{
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
        return [
            AssociationField::new('code_service')->setLabel('Service'),
            TextField::new('nom_utilisateur'),
            TextField::new('prenom_utilisateur'),
            TextField::new('nom_connexion'),
            TextField::new('trigram'),
            TextField::new('password'),
        ];
    }
    
}
