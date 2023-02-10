<?php

namespace App\Form;

use App\Entity\ExternalStatement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

/**
 * @extends AbstractType<Route>
 */
class ExternalStatementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('description', TextType::class, [
                'label' => 'Beschreibung',
            ])
            ->add('file', DropZoneType::class, [
                'label' => 'Datei',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'maxSize' => '5M',
                        'mimeTypesMessage' => 'Es können nur PDF-Dateien hochgeladen werden',
                        'maxSizeMessage' => 'Die Datei darf höchstens 5Mb gross sein',
                    ]),
                ],
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'Öffentlich',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExternalStatement::class,
        ]);
    }
}
