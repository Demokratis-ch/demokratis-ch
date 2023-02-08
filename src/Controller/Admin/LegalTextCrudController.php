<?php

namespace App\Controller\Admin;

use App\Entity\LegalText;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LegalTextCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LegalText::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm(),
            AssociationField::new('consultation'),
            TextField::new('title'),
            TextEditorField::new('text'),
            AssociationField::new('paragraphs')->hideOnIndex(),
            AssociationField::new('importedFrom')->hideOnIndex(),
            AssociationField::new('statement')->setRequired(false),
            DateTimeField::new('createdAt')->hideOnIndex(),
            DateTimeField::new('updatedAt')->hideOnIndex(),
        ];
    }
}
