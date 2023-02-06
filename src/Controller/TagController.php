<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\AddTagType;
use App\Form\CreateTagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    #[Route('/tag/{slug}/add', name: 'app_tag_add', methods: ['GET', 'POST'])]
    public function index(Consultation $consultation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddTagType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()['tags']->getIterator() as $tag) {
                $consultation->addTag($tag);
            }

            $entityManager->persist($consultation);
            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_show_statements', ['slug' => $consultation->getSlug()]);
        }

        return $this->render('tag/add.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/tag/{uuid}/create', name: 'app_tag_create', methods: ['GET', 'POST'])]
    public function create(Consultation $consultation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CreateTagType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $tag->addConsultation($consultation);
            $tag->setApproved(false);

            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_show_statements', ['slug' => $consultation->getSlug()]);
        }

        return $this->render('tag/create.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }
}
