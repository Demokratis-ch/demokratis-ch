<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Entity\Organisation;
use App\Entity\UserOrganisation;
use App\Form\InviteType;
use App\Repository\UserOrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/invite', name: 'app_invite')]
class InviteController extends AbstractController
{
    private $mailer;

    public function __construct(
        MailerInterface $mailer,
        UserRepository $userRepository
    ) {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    #[Route('/{slug}', name: '_add', methods: ['GET', 'POST'])]
    #[isGranted('own', subject: 'organisation')]
    public function add(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Organisation $organisation,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Request $request,
        MailerInterface $mailer,
        UserOrganisationRepository $userOrganisationRepository
    ): Response {
        $invite = new Invite();
        $invite->setOrganisation($organisation);
        $invite->setToken(bin2hex(random_bytes(6)));

        $form = $this->createForm(InviteType::class, $invite);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);

            // Check if the user is already a member of the organisation
            $userOrganisation = $userOrganisationRepository->findOneBy([
                'user' => $user,
                'organisation' => $organisation,
            ]);

            if ($userOrganisation) {
                $this->addFlash('invite-user-exists', $userOrganisation->getUser()->getEmail().' gehört bereits zur Organisation.');

                return $this->redirectToRoute('app_organisation_details', ['slug' => $organisation->getSlug()]);
            }

            // Send the Mail
            $this->sendInvite($invite);

            $entityManager->persist($invite);
            $entityManager->flush();

            return $this->redirectToRoute('app_organisation_details', ['slug' => $organisation->getSlug()]);
        }

        return $this->render('invite/add.html.twig', [
            'form' => $form,
            'organisation' => $organisation,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', methods: ['GET'])]
    public function delete(
        #[MapEntity(mapping: ['id' => 'id'])]
        Invite $invite,
        EntityManagerInterface $entityManager,
    ): Response {
        $organisation = $invite->getOrganisation();

        if (!$this->isGranted('own', $organisation)) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($invite);
        $entityManager->flush();

        return $this->redirectToRoute('app_organisation_details', ['slug' => $organisation->getSlug()]);
    }

    #[Route('/accept/{token}', name: '_accept', methods: ['GET'])]
    public function accept(
        #[MapEntity(mapping: ['token' => 'token'])]
        Invite $invite,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if ($user !== $invite->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to accept this invitation.');
        }

        $organisation = $invite->getOrganisation();

        if (!$organisation) {
            throw $this->createNotFoundException('Organisation not found');
        }

        $invite->setRegisteredAt(new \DateTimeImmutable());
        $entityManager->persist($invite);

        $userOrganisation = new UserOrganisation();
        $userOrganisation->setIsAdmin(false);
        $userOrganisation->setUser($user);
        $userOrganisation->setOrganisation($organisation);
        $entityManager->persist($userOrganisation);

        $entityManager->flush();

        return $this->redirectToRoute('app_index');
    }

    #[Route('/resend/{token}', name: '_resend', methods: ['GET'])]
    public function resend(
        #[MapEntity(mapping: ['token' => 'token'])]
        Invite $invite,
    ): Response {
        $this->sendInvite($invite);

        return $this->redirectToRoute('app_organisation_details', ['slug' => $invite->getOrganisation()->getSlug()]);
    }

    public function sendInvite(Invite $invite): void
    {
        $user = $this->userRepository->findOneBy(['email' => $invite->getUser()]);

        if ($user) {
            // The user exists and is invited to the organisation
            $route = 'app_invite_accept';
        } else {
            $route = 'app_register';
        }

        $email = (new TemplatedEmail())
            ->from('noreply@demokratis.ch')
            ->to(new Address($invite->getEmail()))
            ->subject('Demokratis.ch Einladung')
            ->htmlTemplate('invite/invite_mail.html.twig')
            ->context([
                'organisation' => $invite->getOrganisation()->getName(),
                'url' => $this->generateUrl($route, [
                    'token' => $invite->getToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

        try {
            $this->mailer->send($email);
            $this->addFlash('send-invite-success', 'Die Einladung wurde versendet.');
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('send-invite-error', 'Die Einladung konnte nicht versendet werden.');
        }
    }
}
