<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

/**
 * @extends AbstractType<Route>
 */
class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('colored', CheckboxType::class, [
                'label' => 'Vorgeschlagene Änderungen farblich hervorheben',
                'required' => false,
                'attr' => [
                    'checked' => 'checked',
                ],
            ])
            ->add('reasons', CheckboxType::class, [
                'label' => 'Begründungen der Änderungen mit exportieren',
                'required' => false,
            ])
            ->add('freetext', CheckboxType::class, [
                'label' => 'Freitextfelder mit exportieren',
                'required' => false,
                'attr' => [
                    'checked' => 'checked',
                ],
            ])
            ->add('comments', ChoiceType::class, [
                'label' => 'Kommentare',
                'help' => 'Verschachtelte Kommentare erscheinen als normale Kommentare der ersten Ebene.',
                'choices' => [
                    'Nicht exportieren' => 1,
                    'Als Word-Kommentare' => 2,
                    'Als Fliesstext' => 3,
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Exportieren',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
