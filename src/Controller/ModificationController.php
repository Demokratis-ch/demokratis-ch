<?php

namespace App\Controller;

use App\Entity\ChosenModification;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Paragraph;
use App\Entity\Statement;
use App\Form\AcceptRefuseType;
use App\Form\ModificationType;
use App\Repository\ChosenModificationRepository;
use App\Repository\ModificationRepository;
use App\Repository\ModificationStatementRepository;
use App\Service\WordDiff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/p')]
class ModificationController extends AbstractController
{
    #[Route('/{modification_uuid}/{statement_uuid}/diff', name: 'app_paragraph_diff', methods: ['GET', 'POST'])]
    public function diff(
        #[MapEntity(mapping: ['modification_uuid' => 'uuid'])] Modification $modification,
        #[MapEntity(mapping: ['statement_uuid' => 'uuid'])] Statement $statement,
        Request $request,
        EntityManagerInterface $manager,
        ModificationStatementRepository $modificationStatementRepository
    ): Response {
        $modificationStatement = $modificationStatementRepository->findOneBy(['statement' => $statement, 'modification' => $modification]);

        $wd = new WordDiff();
        $diffs = $wd->diff($modification->getParagraph()->getText(), $modification->getText());

        $form = $this->createForm(AcceptRefuseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$modificationStatement) {
                $modificationStatement = new ModificationStatement();
                $modificationStatement->setStatement($statement);
                $modificationStatement->setModification($modification);
                $modificationStatement->setRefused(false);
                $manager->persist($modificationStatement);
                $manager->flush();
            }

            if (isset($data['reason'])) {
                $modificationStatement->setDecisionReason($data['reason']);
                $manager->persist($modificationStatement);
                $manager->flush();
            }

            if ($form->get('accept')->isClicked()) {
                return $this->redirectToRoute('app_paragraph_accept', ['uuid' => $modificationStatement->getUuid()]);
            } else {
                return $this->redirectToRoute('app_paragraph_refuse', ['uuid' => $modificationStatement->getUuid()]);
            }
        }

        return $this->render('paragraph/diff.html.twig', [
            'diffs' => $diffs,
            'form' => $form,
            'modification' => $modification,
            'statement' => $statement,
            'modificationStatement' => $modificationStatement,
            'consultation' => $statement->getConsultation(),
            'owner' => $statement->getOwners()->contains($this->getUser()),
        ]);
    }

    #[Route('/refuse/{uuid}', name: 'app_paragraph_refuse', methods: ['GET'])]
    public function refuse(ModificationStatement $modificationStatement, EntityManagerInterface $entityManager): Response
    {
        $statement = $modificationStatement->getStatement();

        if (!$this->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $modificationStatement->setRefused(true);
        $entityManager->persist($modificationStatement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $modificationStatement->getModification()->getParagraph()->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($modificationStatement->getModification()->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reopen/{uuid}', name: 'app_paragraph_reopen', methods: ['GET'])]
    public function reopen(ModificationStatement $modificationStatement, EntityManagerInterface $entityManager): Response
    {
        $statement = $modificationStatement->getStatement();

        if (!$this->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $modificationStatement->setRefused(false);
        $entityManager->persist($modificationStatement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $modificationStatement->getModification()->getParagraph()->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($modificationStatement->getModification()->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
    }

    #[Route('/accept/{uuid}', name: 'app_paragraph_accept', methods: ['GET'])]
    public function accept(ModificationStatement $modificationStatement, EntityManagerInterface $entityManager, ChosenModificationRepository $chosenModificationRepository): Response
    {
        $statement = $modificationStatement->getStatement();

        if (!$this->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $paragraph = $modificationStatement->getModification()->getParagraph();

        $chosenModification = $chosenModificationRepository->findOneBy(['paragraph' => $paragraph, 'statement' => $statement]);

        if (!$chosenModification) {
            $chosenModification = new ChosenModification();
            $chosenModification->setParagraph($paragraph);
            $chosenModification->setStatement($statement);
        }

        $chosenModification->setModificationStatement($modificationStatement);
        $chosenModification->setChosenBy($this->getUser());
        $chosenModification->setChosenAt(new \DateTimeImmutable());
        $entityManager->persist($chosenModification);

        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $modificationStatement->getModification()->getParagraph()->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($modificationStatement->getModification()->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/{uuid}', name: 'app_paragraph_remove_chosen', methods: ['GET'])]
    public function removeChosen(ModificationStatement $modificationStatement, EntityManagerInterface $entityManager, ChosenModificationRepository $chosenModificationRepository): Response
    {
        $statement = $modificationStatement->getStatement();

        if (!$this->isGranted('own', $statement)) {
            throw new AccessDeniedException();
        }

        $paragraph = $modificationStatement->getModification()->getParagraph();

        $chosenModification = $chosenModificationRepository->findOneBy(['paragraph' => $paragraph, 'statement' => $statement]);

        if ($chosenModification) {
            $entityManager->remove($chosenModification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $modificationStatement->getModification()->getParagraph()->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($modificationStatement->getModification()->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{uuid}/{statement}', name: 'app_paragraph_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', subject: 'statement')]
    public function index(Paragraph $paragraph, Statement $statement, Request $request, EntityManagerInterface $entityManager, ModificationRepository $modificationRepository): Response
    {
        if (!$this->getUser()) {
            throw new AccessDeniedException();
        }

        $mod = $request->get('mod');
        $modifiedText = $modificationRepository->findOneBy(['uuid' => $mod]);

        $modification = new Modification();
        if ($modifiedText) {
            $modification->setText($modifiedText->getText());
        } elseif (!$modification->getText()) {
            $modification->setText($paragraph->getText());
        }

        $modification->setCreatedBy($this->getUser());
        $modification->setCreatedAt(new \DateTimeImmutable());
        $modification->setParagraph($paragraph);

        $form = $this->createForm(ModificationType::class, $modification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($modification);

            $modificationStatement = new ModificationStatement();
            $modificationStatement->setModification($modification);
            $modificationStatement->setStatement($statement);
            $modificationStatement->setRefused(false);

            $entityManager->persist($modificationStatement);
            $entityManager->flush();

            return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid(), 'lt' => $paragraph->getLegalText()->getUuid()->toBase58(), '_fragment' => substr($modification->getParagraph()->getUuid(), 0, 8)], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paragraph/edit.html.twig', [
            'paragraph' => $paragraph,
            'statement' => $statement,
            'form' => $form,
        ]);
    }
}
