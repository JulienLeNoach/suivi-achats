<?php

namespace App\Controller\Admin;

use App\Entity\Fournisseurs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_OPT_FOURNISSEURS')]


class FournisseursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fournisseurs::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Fournisseurs')
            ->setEntityLabelInSingular('Fournisseur')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des fournisseurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un fournisseur')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer un fournisseur')
            ->setPaginatorPageSize(20);
    }
    public function configureFields(string $pageName): iterable
    {
        if ($pageName === 'index') {
            return [
                TextField::new('nom_fournisseur')->setLabel('Nom du fournisseur'),
                TextField::new('num_siret')->setLabel('Numéro siret'),
                TextField::new('ville'),
            ];
        } else {

            return [
                TextField::new('num_siret')->setLabel('Numéro siret'),
                TextField::new('nom_fournisseur')->setLabel('Nom du fournisseur'),
                TextField::new('ville'),
                TextField::new('code_postal'),
                BooleanField::new('pme')->setLabel('PME ?'),
                TextField::new('tel')->setLabel('Téléphone'),
                TextField::new('FAX'),
                TextField::new('mail'),
                TextField::new('date_maj_fournisseur')->setLabel('Date de mise à jour du fournisseur')
            ];
        }
    }
}
