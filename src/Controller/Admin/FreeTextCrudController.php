<?php

namespace App\Controller\Admin;

use App\Entity\FreeText;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FreeTextCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FreeText::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm(),
            TextField::new('text'),
            ChoiceField::new('position')->setChoices([
                'before' => 'before',
                'after' => 'after',
            ]),
            AssociationField::new('paragraph'),
            AssociationField::new('statement'),
            DateTimeField::new('createdAt'),
        ];
    }
}
