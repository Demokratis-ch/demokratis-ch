<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/login_check', name: 'app_login_check', methods: ['GET', 'POST'])]
    public function check(): Response
    {
        throw new \LogicException('This code should never be reached');
    }

    #[Route('/loginlink/{email}', name: 'app_login_link', methods: ['GET'])]
    public function requestLoginLink(string $email, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user, null, 60 * 60 * 24 * 7 * 4);
            $loginLink = $loginLinkDetails->getUrl();
        } else {
            throw new NotFoundResourceException('User not found');
        }

        return $this->render('security/links.html.twig', [
            'email' => $user->getEmail(),
            'link' => $loginLink,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
