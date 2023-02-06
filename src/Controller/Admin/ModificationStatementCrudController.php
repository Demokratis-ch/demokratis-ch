<?php

namespace App\Controller\Admin;

use App\Entity\ModificationStatement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ModificationStatementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ModificationStatement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            IdField::new('uuid')->hideOnForm(),
            AssociationField::new('modification'),
            AssociationField::new('statement'),
            BooleanField::new('refused'),
            TextField::new('decision_reason'),
        ];
    }
}
