<?php

namespace App\Controller\Admin;

use App\Entity\Paragraph;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ParagraphCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Paragraph::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm()->hideOnIndex(),
            IntegerField::new('position'),
            TextareaField::new('text')->renderAsHtml(),
            AssociationField::new('legalText'),
            DateTimeField::new('createdAt')->hideOnIndex(),
            DateTimeField::new('updatedAt')->hideOnIndex(),
        ];
    }
}
