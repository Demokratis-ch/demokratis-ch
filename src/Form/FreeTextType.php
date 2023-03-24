<?php

namespace App\Form;

use App\Entity\FreeText;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

/**
 * @extends AbstractType<Route>
 */
class FreeTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Text, der angezeigt werden soll',
                'attr' => [
                    'class' => 'block w-full h-2/3 md:h-full rounded-md border-gray-300 py-3 px-4 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FreeText::class,
        ]);
    }
}
