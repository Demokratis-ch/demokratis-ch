<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserOrganisation;
use App\Form\RegistrationFormType;
use App\Repository\InviteRepository;
use App\Repository\UserRepository;
use App\Security\AppFormLoginAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register/{token}', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(string $token, Request $request, InviteRepository $inviteRepository, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppFormLoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response|null
    {
        $invite = $inviteRepository->findOneBy(['token' => $token, 'invitedAt' => null]);

        if (!$invite) {
            throw new NotFoundResourceException('Invite not found');
        }

        $user = new User();
        $user->setEmail($invite->getEmail());

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Mark Invite as registered
            $invite->setRegisteredAt(new \DateTimeImmutable());
            $invite->setUser($user);
            $entityManager->persist($invite);

            if ($invite->getPerson()) {
                $user->setPerson($invite->getPerson());
            }

            if ($invite->getOrganisation()) {
                $userOrganisation = new UserOrganisation();
                $userOrganisation->setUser($user);
                $userOrganisation->setOrganisation($invite->getOrganisation());
                $userOrganisation->setIsAdmin(false);
                $entityManager->persist($userOrganisation);
            }

            // As long as invites are active, we don't want to send a verification email
            $user->setIsVerified(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            /*
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@demokratis.ch', 'Demokratis'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            */

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_index');
    }
}
