<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Discussion;
use App\Entity\Thread;
use App\Form\DiscussionType;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/discussion')]
class DiscussionController extends AbstractController
{
    #[Route('/{id}', name: 'app_discussion_show', methods: ['GET'])]
    public function show(Discussion $discussion): Response
    {
        return $this->render('discussion/show.html.twig', [
            'discussion' => $discussion,
        ]);
    }

    #[Route('add/{slug}', name: 'app_discussion_add', methods: ['GET', 'POST'])]
    public function add(Consultation $consultation, DiscussionRepository $discussionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DiscussionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread = new Thread();

            $discussion = $form->getData();
            $discussion->setConsultation($consultation);
            $discussion->setThread($thread);
            $discussion->setCreatedBy($this->getUser());
            $discussion->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($discussion);

            $entityManager->flush();

            return $this->redirectToRoute('app_discussion_show', ['id' => $discussion->getId()]);
        }

        return $this->render('discussion/add.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }
}
