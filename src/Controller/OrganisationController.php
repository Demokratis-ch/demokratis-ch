<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Entity\UserOrganisation;
use App\Form\MemberType;
use App\Form\OrganisationType;
use App\Repository\InviteRepository;
use App\Repository\OrganisationRepository;
use App\Repository\StatementRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[Route('/organisation')]
class OrganisationController extends AbstractController
{
    #[Route('s', name: 'app_organisation_index', methods: ['GET'])]
    public function index(OrganisationRepository $organisationRepository): Response
    {
        return $this->render('organisation/index.html.twig', [
            'organisations' => $organisationRepository->findBy(['public' => true]),
        ]);
    }

    #[Route('/{slug}', name: 'app_organisation_details', methods: ['GET'])]
    #[isGranted('own', subject: 'organisation')]
    public function details(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        InviteRepository $inviteRepository,
        StatementRepository $statementRepository,
    ): Response {
        return $this->render('organisation/details.html.twig', [
            'organisation' => $organisation,
            'invites' => $inviteRepository->findBy(['organisation' => $organisation, 'registeredAt' => null]),
            'statements' => $statementRepository->findBy(['organisation' => $organisation, 'public' => true]),
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_organisation_edit', methods: ['GET', 'POST'])]
    #[isGranted('own', subject: 'organisation')]
    public function edit(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response {
        $form = $this->createForm(OrganisationType::class, $organisation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organisation = $form->getData();
            $organisation->setSlug(strtolower($slugger->slug($organisation->getName())));
            $entityManager->persist($organisation);
            $entityManager->flush();

            return $this->redirectToRoute('app_organisation_details', ['slug' => $organisation->getSlug()]);
        }

        return $this->render('organisation/edit.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/details/{id}', name: 'app_organisation_member', methods: ['GET', 'POST'])]
    #[isGranted('own', subject: 'organisation')]
    public function member(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserOrganisation $userOrganisation,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response {
        $form = $this->createForm(MemberType::class, $userOrganisation->getUser()->getPerson());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $person = $form->getData();
            $person->setUser($userOrganisation->getUser());
            $entityManager->persist($person);
            $entityManager->flush();

            $this->addFlash(
                'member-person-success',
                'Änderungen gespeichert.',
            );

            return $this->redirectToRoute('app_organisation_member', ['slug' => $organisation->getSlug(), 'id' => $userOrganisation->getId()]);
        }

        return $this->render('organisation/member.html.twig', [
            'form' => $form,
            'organisation' => $organisation,
            'userOrganisation' => $userOrganisation,
        ]);
    }

    #[Route('/{slug}/login/{id}', name: 'app_organisation_member_link', methods: ['GET'])]
    #[isGranted('own', subject: 'organisation')]
    public function loginlink(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserOrganisation $userOrganisation,
        LoginLinkHandlerInterface $loginLinkHandler,
        EmailVerifier $emailVerifier,
    ): Response {
        $user = $userOrganisation->getUser();
        $expiry = 60 * 60 * 2;

        if ($user) {
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user, null, $expiry);
            $loginLink = $loginLinkDetails->getUrl();
        } else {
            throw new NotFoundResourceException('User not found');
        }

        $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@demokratis.ch', 'Demokratis'))
                ->to($user->getEmail())
                ->subject('Demokratis.ch Login Link')
                ->htmlTemplate('organisation/loginlink_email.html.twig')
                ->context([
                    'expiration_date' => new \DateTime('+ '.$expiry.' seconds'),
                    'url' => $loginLink,
                ])
        );

        // Flash
        $this->addFlash(
            'member-person-loginlink',
            'Link per E-Mail zugestellt.',
        );

        return $this->redirectToRoute('app_organisation_member', ['slug' => $organisation->getSlug(), 'id' => $userOrganisation->getId()]);
    }

    #[Route('/{slug}/promote/{id}', name: 'app_organisation_promote', methods: ['GET'])]
    #[isGranted('own', subject: 'organisation')]
    public function promote(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserOrganisation $userOrganisation,
        EntityManagerInterface $entityManager
    ): Response {
        $userOrganisation->setIsAdmin(true);
        $entityManager->persist($userOrganisation);
        $entityManager->flush();

        return $this->redirectToRoute('app_organisation_member', ['slug' => $organisation->getSlug(), 'id' => $userOrganisation->getId()]);
    }

    #[Route('/{slug}/demote/{id}/', name: 'app_organisation_demote', methods: ['GET'])]
    #[isGranted('own', subject: 'organisation')]
    public function demote(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserOrganisation $userOrganisation,
        EntityManagerInterface $entityManager
    ): Response {
        $userOrganisation->setIsAdmin(false);
        $entityManager->persist($userOrganisation);
        $entityManager->flush();

        return $this->redirectToRoute('app_organisation_member', ['slug' => $organisation->getSlug(), 'id' => $userOrganisation->getId()]);
    }

    #[Route('/{slug}/delete/{id}', name: 'app_organisation_delete', methods: ['GET'])]
    #[isGranted('own', subject: 'organisation')]
    public function delete(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        #[MapEntity(mapping: ['id' => 'id'])]
        UserOrganisation $userOrganisation,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($userOrganisation);
        $entityManager->flush();

        return $this->redirectToRoute('app_organisation_details', ['slug' => $organisation->getSlug()]);
    }
}
