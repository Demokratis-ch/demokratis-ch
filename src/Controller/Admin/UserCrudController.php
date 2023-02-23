<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\RouterInterface as Router;

class UserCrudController extends AbstractCrudController
{
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('uuid')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('password')->setFormType(PasswordType::class)->onlyWhenCreating(),
            TextField::new('password')->onlyOnForms(),
            AssociationField::new('person'),
            ArrayField::new('roles'),
            AssociationField::new('organisations')->setFormTypeOption('by_reference', false),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonateUser = Action::new('impersonateUser', 'Impersonate')
            ->linkToUrl(
                fn (User $entity) => $this->router->generate(
                    'app_index',
                    [],
                    true
                ).'?_switch_user='.$entity->getEmail()
            );

        return $actions
            ->add(Crud::PAGE_INDEX, $impersonateUser)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->setPermission(Action::INDEX, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }
}
