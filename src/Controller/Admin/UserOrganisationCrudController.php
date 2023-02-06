<?php

namespace App\Controller\Admin;

use App\Entity\UserOrganisation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class UserOrganisationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserOrganisation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user'),
            AssociationField::new('organisation'),
            BooleanField::new('isAdmin')->renderAsSwitch(false),
        ];
    }
}
