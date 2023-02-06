<?php

namespace App\Controller\Admin;

use App\Entity\Discussion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DiscussionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Discussion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('topic'),
            AssociationField::new('thread'),
            AssociationField::new('consultation'),
            DateTimeField::new('createdAt'),
            AssociationField::new('createdBy'),
        ];
    }
}
