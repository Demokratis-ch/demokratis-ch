<?php

namespace App\Form;

use App\Entity\Invite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Route>
 */
class InviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'help' => 'E-Mail Adresse der Person, die eingeladen werden soll',
                'attr' => [
                    'placeholder' => 'test@test.com',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Die Mailadresse kann nicht leer sein',
                    ]),
                    new Email([
                        'message' => 'Es muss eine gültige Mailadresse angegeben werden',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Einladen',
                'attr' => [
                    'class' => 'items-center rounded-md border border-blue-300 bg-blue-100 px-4 py-2 text-sm font-medium text-blue-500 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invite::class,
        ]);
    }
}
