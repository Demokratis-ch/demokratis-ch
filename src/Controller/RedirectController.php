<?php

namespace App\Controller;

use App\Form\RedirectPasswordType;
use App\Repository\RedirectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class RedirectController extends AbstractController
{
    #[Route('/', name: 'app_redirect', host: '%shorturl_host%', methods: ['GET', 'POST'], priority: 1)]
    #[Route('/', name: 'app_redirect_alt', host: '%alt_host%', methods: ['GET', 'POST'], priority: 1)]
    public function redirectHome(): Response
    {
        throw new NotFoundHttpException();
    }

    #[Route('/{token}', name: 'app_redirect_shorturl', host: '%shorturl_host%', methods: ['GET', 'POST'], priority: 1)]
    #[Route('/{token}', name: 'app_redirect_shorturl_alt', host: '%alt_host%', methods: ['GET', 'POST'], priority: 1)]
    public function shortUrl(RedirectRepository $redirectRepository, Request $request, RouterInterface $router, string $token = null): Response
    {
        $redirect = $redirectRepository->findOneBy(['token' => $token]);
        $host = 'https://'.$this->getParameter('default_host').'/';

        if ($redirect) {
            if ($redirect->getStatement()) {
                $statement_route = $host.$router->generate('app_statement_show', ['uuid' => $redirect->getStatement()->getUuid()], Router::RELATIVE_PATH);
            } elseif ($redirect->getConsultation()) {
                $consultation_route = $host.$router->generate('app_consultation_show_statements', ['slug' => $redirect->getConsultation()->getSlug()], Router::RELATIVE_PATH);
            } elseif ($redirect->getUrl()) {
                return $this->redirect($redirect->getUrl());
            } else {
                throw new NotFoundHttpException('No target');
            }

            if ($redirect->getPassword() !== null) {
                $form = $this->createForm(RedirectPasswordType::class);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $password = $form['password']->getData();
                    $hash = sha1($password);

                    if ($hash === $redirect->getPassword()) {
                        if ($redirect->getStatement()) {
                            return $this->redirect($statement_route);
                        } elseif ($redirect->getConsultation()) {
                            return $this->redirect($consultation_route);
                        }
                    }
                }

                return $this->render('index/redirect.html.twig', [
                    'form' => $form ?? null,
                ]);
            }

            return isset($statement_route) ? $this->redirect($statement_route) : $this->redirect($consultation_route);

        // return
        } else {
            throw new NotFoundHttpException('Not found');
        }
    }
}
