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
        public readonly Paragraph $paragraph,
        public readonly array $freetextBefore,
        public readonly array $freetextAfter,
        public readonly array $openModifications,
        public readonly array $refusedModifications,
        public readonly array $foreignModifications,
        public readonly ?ChosenModification $chosenModification,
        public readonly array $peers, // statements where this modification has been chosen
        public readonly ?array $chosenDiff,
    ) {
    }

    public function getAllModifications(): array
    {
        return [...$this->openModifications, ...$this->refusedModifications, ...$this->foreignModifications];
    }
}
