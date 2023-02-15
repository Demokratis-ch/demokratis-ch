<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'app_newsletter_subscribe', methods: ['GET', 'POST'])]
    public function subscribe(Request $request, NewsletterRepository $newsletterRepository): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter, [
            'action' => $this->generateUrl('app_newsletter_subscribe'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletterRepository->add($newsletter, true);

            return $this->redirectToRoute('app_newsletter_success');
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/newsletter', name: 'app_newsletter_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('newsletter/success.html.twig');
    }

}
