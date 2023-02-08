<?php

namespace App\Controller\Admin;

use App\Entity\Statement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StatementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Statement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm()->hideOnIndex(),
            AssociationField::new('consultation'),
            TextField::new('name'),
            TextField::new('justification')->hideOnIndex(),
            AssociationField::new('organisation'),
            TextField::new('intro'),
            BooleanField::new('public'),
            BooleanField::new('editable'),
        ];
    }
}
