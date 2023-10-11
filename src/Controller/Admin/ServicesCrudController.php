<?php

namespace App\Controller\Admin;

use App\Entity\Services;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_OPT_SERVICES')]
class ServicesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Services::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Services')
            ->setEntityLabelInSingular('Service')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des services')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un service')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editer un service')
            ->setPaginatorPageSize(20);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('code_service'),
            TextField::new('nom_service')->setLabel('Nom du service'),
        ];
    }
    
}
