<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Consultation;
use App\Entity\Discussion;
use App\Entity\Invite;
use App\Entity\LegalText;
use App\Entity\Media;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Newsletter;
use App\Entity\Organisation;
use App\Entity\Paragraph;
use App\Entity\Person;
use App\Entity\Statement;
use App\Entity\Tag;
use App\Entity\Thread;
use App\Entity\UnknownInstitution;
use App\Entity\User;
use App\Entity\UserOrganisation;
use App\Entity\Vote;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/admin', name: 'admin', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Demokratis');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Invites', 'far fa-person-sign', Invite::class);
        yield MenuItem::linkToCrud('Users', 'far fa-users', User::class);
        yield MenuItem::linkToCrud('Persons', 'far fa-people-simple', Person::class);
        yield MenuItem::linkToCrud('Organisation', 'far fa-buildings', Organisation::class);
        yield MenuItem::linkToCrud('User Organisation', 'far fa-link', UserOrganisation::class);
        yield MenuItem::linkToCrud('Newsletter', 'far fa-envelope', Newsletter::class);

        yield MenuItem::section('Consultation');
        yield MenuItem::linkToCrud('Consultations', 'far fa-book', Consultation::class);
        yield MenuItem::linkToCrud('Legal Texts', 'far fa-book-section', LegalText::class);
        yield MenuItem::linkToCrud('Statements', 'far fa-file-lines', Statement::class);
        yield MenuItem::linkToCrud('Paragraphs', 'far fa-line-height', Paragraph::class);
        yield MenuItem::linkToCrud('Modifications', 'far fa-pencil', Modification::class);
        yield MenuItem::linkToCrud('Modification Statements', 'far fa-link', ModificationStatement::class);

        yield MenuItem::section('Tags');
        yield MenuItem::linkToCrud('Tags', 'far fa-tags', Tag::class);
        yield MenuItem::linkToCrud('Unknown institution', 'far fa-square-question', UnknownInstitution::class);

        yield MenuItem::section('Comments');
        yield MenuItem::linkToCrud('Comments', 'far fa-comment', Comment::class);
        yield MenuItem::linkToCrud('Votes', 'far fa-thumbs-up', Vote::class);
        yield MenuItem::linkToCrud('Threads', 'far fa-comments', Thread::class);
        yield MenuItem::linkToCrud('Discussions', 'far fa-message-code', Discussion::class);

        yield MenuItem::section('Other');
        yield MenuItem::linkToCrud('Media', 'far fa-image', Media::class);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            // To customize further, see https://symfony.com/bundles/EasyAdminBundle/current/design.html
            ->addHtmlContentToHead(
                '<style>
                                :root { 
                                    --color-primary: rgb(59, 130, 246); 
                                    --color-text: #000;
                                    --link-color: rgb(59, 130, 246);
                                    --link-hover-color: rgb(9, 80, 195);
                                    --form-switch-checked-bg: rgb(59, 130, 246); 
                                    
                                }
                                
                                .page-item.active .page-link {
                                    background-color: rgb(59, 130, 246);
                                    border-color: rgb(59, 130, 246);
                                }
                                
                                a {
                                    --color-primary: rgb(59, 130, 246); 
                                }
                                
                                span.fa-stack svg {
                                    display: none;
                                } 
                                
                                
                                
                           </style>'
            )
            ->addHtmlContentToHead('<meta name="turbo-root" content="/admin">')
            ->addHtmlContentToHead('<script src="https://kit.fontawesome.com/'.$this->getParameter('app.fontawesome_key').'.js" crossorigin="anonymous"></script>')

        ;
    }
}
