<?php

declare(strict_types=1);

namespace App\Aggregate;

use App\Entity\ChosenModification;
use App\Entity\FreeText;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Paragraph;

class ParagraphAggregate
{
    /**
     * @param array<FreeText>              $freetextBefore
     * @param array<FreeText>              $freetextAfter
     * @param array<Modification>          $openModifications
     * @param array<Modification>          $refusedModifications
     * @param array<Modification>          $foreignModifications
     * @param array<ModificationStatement> $peers
     * @param array<string>|null           $chosenDiff
     */
    public function __construct(
        public Paragraph $paragraph,
        public array $freetextBefore,
        public array $freetextAfter,
        public array $openModifications,
        public array $refusedModifications,
        public array $foreignModifications,
        public ?ChosenModification $chosenModification,
        public array $peers, // statements where this modification has been chosen
        public ?array $chosenDiff,
    ) {
    }

    public function getAllModifications(): array
    {
        return [...$this->openModifications, ...$this->refusedModifications, ...$this->foreignModifications];
    }
}
