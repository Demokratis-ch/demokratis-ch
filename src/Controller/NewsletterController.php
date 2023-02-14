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
    #[Route('/newsletter', name: 'newsletter', methods: ['GET', 'POST'])]
    public function subscribe(Request $request, NewsletterRepository $newsletterRepository): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletterRepository->add($newsletter, true);

            $this->addFlash(
                'success-newsletter',
                'Erfolgreich angemeldet'
            );
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form,
        ]);
    }
}
