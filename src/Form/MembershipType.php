<?php

namespace App\Form;

use App\Entity\Membership;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

/**
 * @extends AbstractType<Route>
 */
class MembershipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Vorname',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nachname',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Strasse',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                ],
            ])
            ->add('zip', TextType::class, [
                'label' => 'Postleitzahl',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                ],
            ])
            ->add('location', TextType::class, [
                'label' => 'Ort',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                ],
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Telefon',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                    'placeholder' => 'optional',
                ],
            ])
            ->add('comment', TextType::class, [
                'required' => false,
                'label' => 'Kommentar',
                'label_attr' => [
                    'class' => 'block text-sm text-gray-700',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
                    'placeholder' => 'optional',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Demokratis beitreten',
                'attr' => [
                    'class' => 'ml-3 inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membership::class,
        ]);
    }
}
