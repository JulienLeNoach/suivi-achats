<?php

namespace App\Controller\Admin;

use App\Entity\UO;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_OPT_UO')]
class UOCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UO::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('UOs')
            ->setEntityLabelInSingular('UOs')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des unités organiques')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une unité organique')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer une unité organique')
            ->setPaginatorPageSize(20);
    }
    
    public function configureFields(string $pageName): iterable
    {

    
        if ($pageName === Crud::PAGE_NEW) {
            // Add 'code_uo' field only on the 'New' page
            $fields[] = IdField::new('code_uo')->setLabel("Code de l'unité organique");
            $fields[] =  TextField::new('libelle_uo')->setLabel("Libellé de l'unité organique");
                }
        if ($pageName === Crud::PAGE_INDEX) {
            // Add 'code_uo' field only on the 'New' page
            $fields[] = IdField::new('code_uo')->setLabel("Code de l'unité organique");
            $fields[] =  TextField::new('libelle_uo')->setLabel("Libellé de l'unité organique");

        }
        if ($pageName === Crud::PAGE_EDIT) {
            // Add 'code_uo' field only on the 'New' page
            $fields[] =  TextField::new('libelle_uo')->setLabel("Libellé de l'unité organique");

        }
        return $fields;
    }
    
}
