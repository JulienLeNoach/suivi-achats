<?php

namespace App\Controller\Admin;

use App\Entity\Formations;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[IsGranted('ROLE_OPT_FORMATIONS')]

class FormationsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        
        return Formations::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Formations')
            ->setEntityLabelInSingular('Formation')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des formations')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une formation')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer une formation')
            ->setPaginatorPageSize(20);
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_NEW) {

            $fields[] = TextField::new('code_formation')->setLabel('Code de la formation');
            $fields[] = TextField::new('libelle_formation')->setLabel('Libellé de la formation');
            $fields[] =BooleanField::new('etat_formation')->setLabel('Etat de la formation');
        }
        if ($pageName === Crud::PAGE_EDIT) {

            $fields[] =TextField::new('code_formation')->setLabel('Code de la formation')->onlyOnIndex();
            $fields[] =TextField::new('libelle_formation')->setLabel('Libellé de la formation');
            $fields[] =BooleanField::new('etat_formation')->setLabel('Etat de la formation');
        
        }
        if ($pageName === Crud::PAGE_INDEX) {

            $fields[] =TextField::new('code_formation')->setLabel('Code de la formation')->onlyOnIndex();
            $fields[] =TextField::new('libelle_formation')->setLabel('Libellé de la formation');
        
        }
        return $fields;

    }
    
}
