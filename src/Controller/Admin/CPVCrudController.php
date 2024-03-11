<?php

namespace App\Controller\Admin;

use App\Entity\CPV;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

#[IsGranted('ROLE_OPT_CPV')]

class CPVCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CPV::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('CPVs')
            ->setEntityLabelInSingular('CPV')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des CPVs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un CPV')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer un CPV')
            ->setPaginatorPageSize(20);
    }


    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                TextField::new('code_cpv')
                    ->setLabel('Code du CPV'),
                TextField::new('libelle_cpv')->setLabel('Libellé du CPV'),
                IntegerField::new('mt_cpv')->setLabel('Montant du CPV'),
            ];
        }
        if ($pageName === Crud::PAGE_NEW) {

            return [
                BooleanField::new('etat_cpv')->setLabel('Etat du CPV'),
                TextField::new('libelle_cpv')->setLabel('Libellé du CPV'),
                IntegerField::new('mt_cpv')->setLabel('Montant du CPV'),
            ];
        }
        if ($pageName === Crud::PAGE_EDIT) {

            return [
                BooleanField::new('etat_cpv')->setLabel('Etat du CPV'),
                TextField::new('libelle_cpv')->setLabel('Libellé du CPV'),
                IntegerField::new('mt_cpv')->setLabel('Montant du CPV'),
            ];
        }
    }
}
