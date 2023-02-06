<?php

namespace App\Controller\Admin;

use App\Entity\UnknownInstitution;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UnknownInstitutionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UnknownInstitution::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('institution'),
            AssociationField::new('consultation'),
            DateTimeField::new('createdAt'),
        ];
    }
}
