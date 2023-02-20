<?php

namespace App\Controller\Admin;

use App\Entity\Membership;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MembershipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Membership::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('firstname'),
            TextField::new('lastname'),
            EmailField::new('email'),
            TextField::new('street'),
            TextField::new('zip'),
            TextField::new('location'),
            TextField::new('phone')->hideOnIndex(),
            TextField::new('comment'),
            DateTimeField::new('createdAt'),
            AssociationField::new('createdBy')->setRequired(false)->hideOnIndex(),
            BooleanField::new('accepted'),
            DateTimeField::new('acceptedAt')->hideOnIndex(),
            AssociationField::new('acceptedBy')->setRequired(false)->hideOnIndex(),
        ];
    }
}
