<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\ConsultationRepository;
use App\Repository\NewsletterRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET', 'POST'])]
    public function index(Request $request, NewsletterRepository $newsletterRepository, ConsultationRepository $consultationRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_consultation');
        }

        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletterRepository->add($newsletter, true);

            $this->addFlash(
                'newsletter',
                'Jetz bisse drin'
            );

            return $this->redirectToRoute('app_index');
        }

        return $this->render('index/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/landing', name: 'app_test', methods: ['GET', 'POST'])]
    public function test(
        ConsultationRepository $consultationRepository,
        Request $request,
        NewsletterRepository $newsletterRepository,
        TagRepository $tagRepository,
    ): Response {
        $consultations = array_slice($consultationRepository->findBy(['status' => 'ongoing'], ['startDate' => 'DESC']), 0, 6);

        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletterRepository->add($newsletter, true);

            $this->addFlash(
                'newsletter',
                'Jetz bisse drin'
            );

            return $this->redirectToRoute('app_index');
        }

        return $this->render('index/test.html.twig', [
            'consultations' => $consultations,
            'count' => $consultationRepository->count(),
            'ongoing' => $consultationRepository->count('ongoing'),
            'planned' => $consultationRepository->count('planned'),
            'tags' => $tagRepository->findBy(['approved' => true]),
            'form' => $form,
        ]);
    }
}
