<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            IdField::new('uuid')->hideOnForm(),
            AssociationField::new('consultation'),
            TextField::new('title'),
            TextField::new('type'),
            TextField::new('filepath'),
            TextField::new('fedlexUri'),
            TextField::new('filename'),
            TextField::new('imported'),
            TextField::new('localFilename'),
        ];
    }
}
