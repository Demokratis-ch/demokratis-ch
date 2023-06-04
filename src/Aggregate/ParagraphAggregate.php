<?php

declare(strict_types=1);

namespace App\Aggregate;

use App\Entity\ChosenModification;
use App\Entity\FreeText;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Paragraph;
use App\Service\WordDiff;

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

    /**
     * updates the paragraphContainer after accepting a modification, so we don't have to reload everything from the db.
     */
    public function changeChosenModification(ChosenModification|null $newChosenModification, Modification|null $oldModification): void
    {
        $this->chosenModification = $newChosenModification;

        if ($newChosenModification !== null) {
            // remove new chosen from open
            foreach ($this->openModifications as $key => $openModification) {
                if ($openModification->getId() === $this->chosenModification->getModification()->getId()) {
                    unset($this->openModifications[$key]);
                    break;
                }
            }
        }

        // add old chosen to open
        if ($oldModification !== null) {
            $this->openModifications[] = $oldModification;
        }

        usort($this->openModifications, fn (Modification $a, Modification $b) => $b->getCreatedAt() <=> $a->getCreatedAt());
        $this->openModifications = array_values($this->openModifications);

        // update chosen diff
        // todo: diff should be done on the fly. currently it is being serialized and sent between the server and client every time
        if ($newChosenModification === null) {
            $this->chosenDiff = null;
        } else {
            $this->chosenDiff = (new WordDiff())->diff($this->paragraph->getText(), $this->chosenModification->getModificationStatement()->getModification()->getText());
        }
    }
}
