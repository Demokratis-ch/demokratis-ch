<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Aggregate\ParagraphAggregate;
use App\Entity\LegalText;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Statement;
use App\Form\ModificationType;
use App\Repository\ModificationRepository;
use App\Repository\ModificationStatementRepository;
use App\Service\ModificationService;
use App\Service\WordDiff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('paragraph')]
class ParagraphComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    // i, paragraphContainer, collapse, statement, legalText
    #[LiveProp(writable: false)]
    public int $i;

    #[LiveProp(writable: true)]
    public ParagraphAggregate $paragraphContainer;

    #[LiveProp(writable: false)]
    public bool $collapse;

    #[LiveProp(writable: false)]
    public Statement $statement;

    #[LiveProp(writable: false)]
    public LegalText $legalText;

    #[LiveProp(writable: true)]
    public int|null $selectedModificationId = null;

    #[LiveProp(writable: true)]
    public bool $showMoreModifications = false;

    #[LiveProp(writable: true)]
    public bool $editMode = false;

    public function __construct(
        private ModificationRepository $modificationRepository,
        private ModificationStatementRepository $modificationStatementRepository,
        private EntityManagerInterface $entityManager,
        private ModificationService $modificationService,
    ) {
    }

    #[LiveAction]
    public function selectModification(#[LiveArg] int $modificationId): void
    {
        $this->selectedModificationId = $modificationId;
    }

    #[LiveAction]
    public function toggleShowMoreModifications(#[LiveArg] bool $showMore): void
    {
        $this->showMoreModifications = $showMore;
    }

    public function getSelectedModification(): ?Modification
    {
        if ($this->selectedModificationId === null) {
            return $this->paragraphContainer->chosenModification?->getModification();
        }

        return $this->modificationRepository->find($this->selectedModificationId);
    }

    public function isSelectedModification(Modification $modification): bool
    {
        $selected = $this->getSelectedModification();
        if ($selected === null) {
            return false;
        }

        return $selected->getId() === $modification->getId();
    }

    public function isNonChosenModificationSelected(): bool
    {
        if ($this->selectedModificationId === null) {
            return false;
        }

        return $this->paragraphContainer->chosenModification?->getModification()?->getId() !== $this->selectedModificationId;
    }

    public function getDiff(): array|null
    {
        $selectedModification = $this->getSelectedModification();
        if ($selectedModification === null) {
            return $this->paragraphContainer->chosenDiff;
        }

        return (new WordDiff())->diff(
            $this->paragraphContainer->paragraph->getText(),
            $selectedModification->getText(),
        );
    }

    public function getPeers(): array
    {
        $selectedModification = $this->getSelectedModification();
        if ($selectedModification === null) {
            return $this->paragraphContainer->peers;
        }

        return $this->modificationStatementRepository->findPeers($this->statement, [$this->getSelectedModification()])[$this->getSelectedModification()->getId()] ?? [];
    }

    #[LiveAction]
    public function createModification(): void
    {
        $this->editMode = true;
    }

    #[LiveAction]
    public function cancelModification(): void
    {
        $this->editMode = false;
    }

    #[LiveAction]
    public function formSubmit(): void
    {
        $this->saveModification();
    }

    private function saveModification(): void
    {
        $this->submitForm();

        /** @var Modification $modification */
        $modification = $this->getFormInstance()->getData();

        if (!($modification instanceof Modification)) {
            throw new \RuntimeException('Not a modification object');
        }

        $modification->setParagraph($this->paragraphContainer->paragraph);
        $modification->setCreatedBy($this->getUser());

        $modificationStatement = new ModificationStatement();
        $modificationStatement->setModification($modification);
        $modificationStatement->setStatement($this->statement);
        $modificationStatement->setRefused(false);

        $this->entityManager->persist($modification);
        $this->entityManager->persist($modificationStatement);
        $this->entityManager->flush();

        $this->editMode = false;
        $this->selectedModificationId = $modification->getId();
        array_unshift($this->paragraphContainer->openModifications, $modification);
    }

    protected function instantiateForm(): FormInterface
    {
        $modification = new Modification();
        $modification->setText($this->getTextForNewModification());

        return $this->createForm(ModificationType::class, $modification);
    }

    private function getTextForNewModification(): string
    {
        return (string) (
            $this->getSelectedModification()?->getText()
            ?? $this->paragraphContainer->chosenModification?->getModification()?->getText()
            ?? $this->paragraphContainer->paragraph->getText()
        );
    }

    #[LiveAction]
    public function acceptSelectedModification(): void
    {
        $selectedModification = $this->getSelectedModification();
        $modificationStatement = $this->modificationStatementRepository->findOneBy([
            'modification' => $selectedModification->getId(),
            'statement' => $this->statement->getId(),
        ]);

        if ($modificationStatement === null) {
            // todo: allow accepting foreign modifications?
            return;
        }

        $oldModification = $this->paragraphContainer->chosenModification?->getModification();

        $newChosen = $this->modificationService->accept($modificationStatement);

        $this->paragraphContainer->changeChosenModification($newChosen, $oldModification);
        $this->selectedModificationId = null;
    }

    #[LiveAction]
    public function resetParagraph(): void
    {
        $chosenModification = $this->paragraphContainer->chosenModification;
        if ($chosenModification === null) {
            throw new \LogicException('Trying to reset a paragraph with no chosen modification.');
        }

        $this->modificationService->resetParagraph($chosenModification);
        $this->paragraphContainer->changeChosenModification(null, $chosenModification->getModification());
        $this->selectedModificationId = null;
    }
}
