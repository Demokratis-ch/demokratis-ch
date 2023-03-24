<?php

namespace App\Controller;

use App\Entity\FreeText;
use App\Entity\Paragraph;
use App\Entity\Statement;
use App\Form\FreeTextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FreeTextController extends AbstractController
{
    #[Route('/free/{uuid}/edit', name: 'app_freetext_edit', methods: ['GET', 'POST'])]
    public function edit(FreeText $freetext, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !$this->isGranted('edit', $freetext->getStatement())) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(FreeTextType::class, $freetext);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($freetext);
            $entityManager->flush();

            return $this->redirectToRoute('app_statement_show', ['uuid' => $freetext->getStatement()->getUuid(), 'lt' => $freetext->getParagraph()->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($freetext->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
        }

        return $this->render('free_text/new.html.twig', [
            'form' => $form,
            'statement' => $freetext->getStatement(),
        ]);
    }

    #[Route('/free/{uuid}/delete', name: 'app_freetext_delete', methods: ['GET'])]
    public function delete(FreeText $freetext, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !$this->isGranted('own', $freetext->getStatement())) {
            throw new AccessDeniedException();
        }

        $entityManager->remove($freetext);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $freetext->getStatement()->getUuid(), 'lt' => $freetext->getParagraph()->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($freetext->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
    }

    #[Route('/free/{uuid}/{statement}', name: 'app_freetext_new', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'statement')]
    public function new(Paragraph $paragraph, Statement $statement, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            throw new AccessDeniedException();
        }

        $freetext = new FreeText();
        $freetext->setParagraph($paragraph);
        $freetext->setStatement($statement);

        $position = $request->query->get('p');

        if (!$position) {
            $position = 'before';
        } elseif ($position == 'after') {
            $position = 'after';
        }

        $freetext->setPosition($position);

        $form = $this->createForm(FreeTextType::class, $freetext);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($freetext);
            $entityManager->flush();

            return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $paragraph->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($paragraph->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
        }

        return $this->render('free_text/new.html.twig', [
            'form' => $form,
            'statement' => $statement,
        ]);
    }
}
