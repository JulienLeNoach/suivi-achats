<?php

namespace App\Controller\Admin;

use App\Entity\Fournisseurs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
        if ($pageName === Crud::PAGE_NEW) {

            return [
                BooleanField::new('etat_fournisseur'),
                TextField::new('num_siret')->setLabel('Numéro siret'),
                TextField::new('nom_fournisseur')->setLabel('Nom du fournisseur'),
                TextField::new('code_client')->setLabel('Code client'),
                TextField::new('code_postal'),
                BooleanField::new('pme')->setLabel('PME ?')              ,
                TextField::new('tel')->setLabel('Téléphone'),
                TextField::new('num_chorus_fournisseur'), 
                TextField::new('mail'),
            ];
        
        }
        if ($pageName === Crud::PAGE_INDEX) {

            return [
                
                TextField::new('num_siret')->setLabel('Numéro siret')->onlyOnIndex(),
                TextField::new('nom_fournisseur')->setLabel('Nom du fournisseur'),
                TextField::new('code_client')->setLabel('Code client'),
                TextField::new('code_postal'),
                TextField::new('tel')->setLabel('Téléphone'),
                TextField::new('num_chorus_fournisseur'), 
                TextField::new('mail'),
            ];
        

        }
        if ($pageName === Crud::PAGE_EDIT) {

            return [
                BooleanField::new('etat_fournisseur'),
                TextField::new('num_siret')->setLabel('Numéro siret'),
                TextField::new('nom_fournisseur')->setLabel('Nom du fournisseur'),
                TextField::new('code_client')->setLabel('Code client'),
                TextField::new('code_postal'),
                BooleanField::new('pme')->setLabel('PME ?')              ,
                TextField::new('tel')->setLabel('Téléphone'),
                TextField::new('num_chorus_fournisseur'), 
                TextField::new('mail'),
            ];
        

        }
    }
}
