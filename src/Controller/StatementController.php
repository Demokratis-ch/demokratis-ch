<?php

namespace App\Controller;

use App\Aggregate\ParagraphAggregate;
use App\Entity\Consultation;
use App\Entity\FreeText;
use App\Entity\Modification;
use App\Entity\Organisation;
use App\Entity\Statement;
use App\Form\StatementIntroType;
use App\Form\StatementType;
use App\Repository\ChosenModificationRepository;
use App\Repository\DocumentRepository;
use App\Repository\ExternalStatementRepository;
use App\Repository\FreeTextRepository;
use App\Repository\LegalTextRepository;
use App\Repository\ModificationRepository;
use App\Repository\ModificationStatementRepository;
use App\Repository\ParagraphRepository;
use App\Repository\StatementRepository;
use App\Service\WordDiff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/statement', name: 'app_statement')]
class StatementController extends AbstractController
{
    #[Route('s', name: '_index', methods: ['GET', 'POST'])]
    public function index(StatementRepository $statementRepository, ExternalStatementRepository $externalStatementRepository): Response
    {
        $statements = $statementRepository->findBy(['public' => true]);
        $externalStatements = $externalStatementRepository->findBy(['public' => true]);

        return $this->render('statement/index.html.twig', [
            'statements' => $statements,
            'externalStatements' => $externalStatements,
        ]);
    }

    #[Route('/{uuid}/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Consultation $consultation, Request $request, EntityManagerInterface $entityManager, StatementRepository $statementRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // If the user is not part of an organisation, yet, create a personal organisation
        if (!$user->getActiveOrganisation()) {
            $organisation = new Organisation();
            $organisation->setName($user->getEmail());
            $organisation->setSlug($user->getEmail());
            $organisation->setPublic(false);
            $organisation->setIsPersonalOrganisation(true);
            $entityManager->persist($organisation);

            $user->setActiveOrganisation($organisation);
            $entityManager->persist($user);

            $entityManager->flush();
        }

        $statement = new Statement();
        $statement->setConsultation($consultation);
        $statement->addOwner($user);
        $statement->setOrganisation($user->getActiveOrganisation());
        $form = $this->createForm(StatementType::class, $statement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $statementRepository->add($statement, true);

            return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('statement/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: '_show', methods: ['GET'])]
    #[IsGranted('view', subject: 'statement')]
    public function show(Statement $statement, DocumentRepository $documentRepository, LegalTextRepository $legalTextRepository, ParagraphRepository $paragraphRepository, ModificationRepository $modificationRepository, ModificationStatementRepository $modificationStatementRepository, ChosenModificationRepository $chosenModificationRepository, FreeTextRepository $freeTextRepository, Request $request): Response
    {
        $approvals = $statement->getApprovedBy();

        if ($this->getUser()) {
            $approved = $approvals->contains($this->getUser());
        } else {
            $approved = false;
        }

        $proposals = $documentRepository->findBy(['consultation' => $statement->getConsultation(), 'type' => 'proposal']);
        $legalTexts = $legalTextRepository->findBy(['consultation' => $statement->getConsultation()]);

        $lt = $request->query->get('lt');
        $legalText = $legalTextRepository->findOneBy(['uuid' => $lt]);

        if (!$legalText) {
            $legalText = $legalTextRepository->findOneBy(['consultation' => $statement->getConsultation()]);
        }

        $paragraphsInLegalText = $paragraphRepository->findBy(['legalText' => $legalText], ['position' => 'ASC']);
        $paragraphs = [];
        $collapse = true;

        $freeTexts = $freeTextRepository->findFreeTextsByStatementIndexed($statement);
        $openModifications = $modificationRepository->findOpenModificationsIndexed($statement);
        $refusedModifications = $modificationRepository->findRefusedModificationsIndexed($statement);
        $foreignModifications = $modificationRepository->findForeignModificationsIndexed($statement);
        $chosens = $chosenModificationRepository->findByStatementIndexed($statement);
        $peersIndexed = $modificationStatementRepository->findPeers($statement, array_values($chosens));

        foreach ($paragraphsInLegalText as $i => $paragraph) {
            $chosen = $chosens[$paragraph->getId()] ?? null;
            $foreign = $foreignModifications[$paragraph->getId()] ?? [];
            if ($chosen) {
                $wd[$i] = new WordDiff();
                $diff = $wd[$i]->diff($paragraph->getText(), $chosen->getModificationStatement()->getModification()->getText());
                $peers = $peersIndexed[$chosen->getModificationStatement()->getModification()->getId()] ?? [];

                // Remove chosen from foreign modifications
                $foreign = array_filter($foreign, fn (Modification $mod) => $mod->getUuid() !== $chosen->getModificationStatement()->getModification()->getUuid());
            }

            $paragraphs[] = new ParagraphAggregate(
                $paragraph,
                $freeTexts[$paragraph->getId()][FreeText::POSITION_BEFORE] ?? [],
                $freeTexts[$paragraph->getId()][FreeText::POSITION_AFTER] ?? [],
                $openModifications[$paragraph->getId()] ?? [],
                $refusedModifications[$paragraph->getId()] ?? [],
                $foreign,
                $chosen,
                $peers ?? [],
                $diff ?? null,
            );
        }

        return $this->render('statement/show.html.twig', [
            'proposals' => $proposals,
            'documents' => $documentRepository->findBy(['consultation' => $statement->getConsultation(), 'type' => 'document']),
            'paragraphs' => $paragraphs,
            'legalText' => $legalText,
            'legalTexts' => $legalTexts,
            'statement' => $statement,
            'approved' => $approved,
            'approvals' => $approvals,
        ]);
    }

    #[Route('/intro/add/{uuid}', name: '_intro_add', methods: ['GET', 'POST'])]
    public function addIntro(Statement $statement, Request $request, StatementRepository $statementRepository): Response
    {
        $form = $this->createForm(StatementIntroType::class, $statement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $statementRepository->add($statement, true);

            return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('statement/intro.html.twig', [
            'statement' => $statement,
            'form' => $form,
        ]);
    }

    #[Route('/approve/{uuid}', name: '_approve', methods: ['GET'])]
    #[IsGranted('own', subject: 'statement')]
    public function approve(Statement $statement, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $statement->addApprovedBy($user);
        $entityManager->persist($statement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/publish/{uuid}', name: '_publish', methods: ['GET'])]
    #[IsGranted('own', subject: 'statement')]
    public function publish(Statement $statement, EntityManagerInterface $entityManager): Response
    {
        $statement->setPublic(true);
        $entityManager->persist($statement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/retract/{uuid}', name: '_retract', methods: ['GET'])]
    #[IsGranted('own', subject: 'statement')]
    public function retract(Statement $statement, EntityManagerInterface $entityManager): Response
    {
        $statement->setPublic(false);
        $statement->setEditable(false);
        $entityManager->persist($statement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/editable/{uuid}', name: '_accept_edits', methods: ['GET'])]
    #[IsGranted('own', subject: 'statement')]
    public function editable(Statement $statement, EntityManagerInterface $entityManager): Response
    {
        $statement->setEditable(true);
        $entityManager->persist($statement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/noteditable/{uuid}', name: '_prevent_edits', methods: ['GET'])]
    #[IsGranted('own', subject: 'statement')]
    public function notEditable(Statement $statement, EntityManagerInterface $entityManager): Response
    {
        $statement->setEditable(false);
        $entityManager->persist($statement);
        $entityManager->flush();

        return $this->redirectToRoute('app_statement_show', ['uuid' => $statement->getUuid()], Response::HTTP_SEE_OTHER);
    }
}
