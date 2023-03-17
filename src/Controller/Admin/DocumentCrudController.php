<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->onlyOnIndex(),
            AssociationField::new('consultation'),
            TextField::new('title'),
            ImageField::new('localFilename')
                ->setBasePath('/uploads/')->setUploadDir('public/uploads/proposals/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setFormTypeOptions(['attr' => ['accept' => 'application/pdf']])
                ->setRequired(true)
                ->setHelp('Es können nur PDF-Dateien hochgeladen werden.')
                ->onlyWhenCreating(),

            ChoiceField::new('type')->setChoices([
                'Dokument' => 'document',
                'Vernehmlassungsvorlage' => 'proposal',
            ]),
            TextField::new('filepath')->hideOnIndex(),
            TextField::new('fedlexUri')->hideOnIndex(),
            TextField::new('filename')->hideonIndex(),
            ChoiceField::new('imported')->setChoices([
                'Fetched' => 'fetched',
                'Paragraphed' => 'paragraphed',
            ]),
        ];
    }
}
