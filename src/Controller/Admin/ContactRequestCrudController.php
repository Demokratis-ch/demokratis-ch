<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactRequest::class;
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
