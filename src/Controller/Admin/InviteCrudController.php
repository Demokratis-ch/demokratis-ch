<?php

namespace App\Controller\Admin;

use App\Entity\Invite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InviteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invite::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('token'),
            AssociationField::new('user')->setRequired(false),
            AssociationField::new('person')->setRequired(false),
            DateTimeField::new('invitedAt'),
            DateTimeField::new('registeredAt'),
        ];
    }
}
