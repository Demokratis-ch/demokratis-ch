<?php

namespace App\Form;

use App\Entity\Organisation;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name der Organisation',
                'help' => 'Achtung: Änderungen am Namen verändern die URL der Organisation. Existierende Links werden ungültig.',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Kurze Beschreibung der Organisation',
                'required' => false,
                'attr' => [
                    'class' => 'mt-1 block w-full max-h-48 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm',
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'Webseite',
                'required' => false,
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags / Schlagworte',
                'class' => Tag::class,
                'multiple' => true,
                'autocomplete' => true,
            ])

            // ->add('logo')
            ->add('submit', SubmitType::class, [
                'label' => 'Änderungen speichern',
                'attr' => [
                    'class' => 'items-center rounded-md border border-blue-300 bg-blue-100 px-4 py-2 text-sm font-medium text-blue-500 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organisation::class,
        ]);
    }
}
