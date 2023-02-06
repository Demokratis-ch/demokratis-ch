<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Überschrift',
                'help' => 'Überschrift oder kurze Beschreibung der Medieninhalte',
            ])
            ->add('url', UrlType::class, [
                'label' => 'Medienbericht',
                'help' => 'Link zu einem Medienbericht mit Bezug zu dieser Vernehmlassung',
            ])
            ->add('submit', SubmitType::class, [
            'label' => 'Speichern',
              'attr' => [
                  'class' => 'inline-flex items-center rounded-md border border-blue-300 bg-blue-100 px-4 py-2 text-sm font-medium text-blue-500 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
              ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
