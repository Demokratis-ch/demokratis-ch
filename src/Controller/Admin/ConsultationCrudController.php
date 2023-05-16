<?php

namespace App\Controller\Admin;

use App\Entity\Consultation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConsultationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Consultation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm()->hideOnIndex(),
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex(),
            TextField::new('fedlexId')->hideOnIndex(),
            AssociationField::new('organisation')->setRequired(false),
            AssociationField::new('singleStatement')->setRequired(false),
            TextField::new('humanTitle')->hideOnIndex(),
            TextField::new('description')->hideOnIndex(),
            ChoiceField::new('status')->setChoices([
                'Geplant' => 'planned',
                'Laufend' => 'ongoing',
                'Abgeschlossen – abwarten Stellungnahmen / Ergebnisbericht' => 'pending_statements_report',
                'Abgeschlossen – abwarten Ergebnisbericht' => 'pending_report',
                'Abgeschlossen' => 'done',
                'Unbekannt' => 'unknown',
        ])->setRequired(true),
            DateField::new('startDate'),
            DateField::new('endDate')->setRequired(true),
            AssociationField::new('documents')->hideOnIndex(),
            TextField::new('office'),
            TextField::new('institution'),
        ];
    }
}
