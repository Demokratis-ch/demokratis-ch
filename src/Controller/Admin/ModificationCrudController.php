<?php

namespace App\Controller\Admin;

use App\Entity\Modification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ModificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Modification::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm()->hideOnIndex(),
            TextareaField::new('text')->renderAsHtml(),
            TextField::new('justification')->hideOnIndex(),
            AssociationField::new('paragraph'),
            AssociationField::new('modificationStatements'),
            AssociationField::new('createdBy'),
            DateTimeField::new('createdAt')->hideOnIndex(),
        ];
    }
}
