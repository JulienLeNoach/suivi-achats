<?php

namespace App\Controller\Admin;

use App\Entity\Achat;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AchatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Achat::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
