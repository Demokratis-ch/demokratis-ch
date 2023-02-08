<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('text'),
            AssociationField::new('author'),
            DateTimeField::new('createdAt')->hideOnIndex(),
            AssociationField::new('thread'),
            AssociationField::new('parent')->setRequired(false),
            DateTimeField::new('deletedAt')->hideOnIndex(),
            AssociationField::new('deletedBy')->setRequired(false),
        ];
    }
}
