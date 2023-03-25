<?php

namespace App\Controller\Admin;

use App\Entity\Redirect;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\RouterInterface as Router;

class RedirectCrudController extends AbstractCrudController
{
    public function __construct(
        public Router $router,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Redirect::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $clearPassword = Action::new('clearPassword', 'Passwort entfernen')
            ->linkToCrudAction('clearPassword');

        return $actions
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->setPermission(Action::INDEX, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->add(Crud::PAGE_INDEX, $clearPassword)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm(),
            TextField::new('token'),
            AssociationField::new('consultation')->setRequired(false),
            AssociationField::new('statement')->setRequired(false),
            AssociationField::new('createdBy'),
            DateTimeField::new('createdAt'),
        ];

        $password = TextField::new('password')
            ->setFormType(PasswordType::class)
            ->setFormTypeOptions([
                'label' => 'Passwort',
                'mapped' => false,
            ])
            ->setRequired(false)
        ;

        $fields[] = $password;

        return $fields;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword(): \Closure
    {
        return function (FormEvent $event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = sha1($password);
            $form->getData()->setPassword($hash);
        };
    }

    public function clearPassword(AdminContext $context)
    {
        $redirect = $context->getEntity()->getInstance();
        $manager = $this->container->get('doctrine')->getManager();

        $redirect->setPassword(null);
        $manager->persist($redirect);
        $manager->flush();

        return $this->redirect($this->router->generate('admin', [
            'crudAction' => 'index',
            'crudControllerFqcn' => 'App\\Controller\\Admin\\RedirectCrudController',
        ]));
    }
}
