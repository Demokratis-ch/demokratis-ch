<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Aggregate\ParagraphAggregate;
use App\Entity\LegalText;
use App\Entity\Modification;
use App\Entity\Statement;
use App\Repository\ModificationRepository;
use App\Repository\ModificationStatementRepository;
use App\Service\WordDiff;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('paragraph')]
class ParagraphComponent extends AbstractController
{
    use DefaultActionTrait;

    // i, paragraphContainer, collapse, statement, legalText
    #[LiveProp(writable: false)]
    public int $i;

    #[LiveProp(writable: false)]
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

    public function __construct(
        private ModificationRepository $modificationRepository,
        private ModificationStatementRepository $modificationStatementRepository,
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
}
