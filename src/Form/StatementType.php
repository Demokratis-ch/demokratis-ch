<?php

namespace App\Form;

use App\Entity\Statement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Überschrift der Stellungnahme',
                ],
            ])
            ->add('justification', TextType::class, [
                'label' => 'Begründung',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Optional',
                ],
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'Die Stellungnahme ist sofort & bereits bei der Ausarbeitung öffentlich einsehbar',
                'required' => false,
            ])
            ->add('editable', CheckboxType::class, [
                'label' => 'Die Stellungnahme für Vorschläge von Dritten öffnen',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Statement::class,
        ]);
    }
}
