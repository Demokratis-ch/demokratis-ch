<?php

namespace App\Entity;

use App\Repository\ChosenModificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ChosenModificationRepository::class)]
#[UniqueEntity(
    fields: ['paragraph', 'statement'],
    message: 'This paragraph already has a chosen modification.',
    errorPath: 'paragraph',
)]
class ChosenModification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chosenModifications')]
    private ?Paragraph $paragraph = null;

    #[ORM\ManyToOne(inversedBy: 'chosenModifications')]
    private ?Statement $statement = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $chosenAt = null;

    #[ORM\ManyToOne(inversedBy: 'chosenModifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $chosenBy = null;

    #[ORM\OneToOne(inversedBy: 'chosen', cascade: ['persist'])]
    private ?ModificationStatement $modificationStatement = null;

    public function __construct()
    {
    }

    public function __toString(): string
    {
        return strval($this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParagraph(): ?Paragraph
    {
        return $this->paragraph;
    }

    public function setParagraph(?Paragraph $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }

    public function getStatement(): ?Statement
    {
        return $this->statement;
    }

    public function setStatement(?Statement $statement): self
    {
        $this->statement = $statement;

        return $this;
    }

    public function getChosenAt(): ?\DateTimeImmutable
    {
        return $this->chosenAt;
    }

    public function setChosenAt(\DateTimeImmutable $chosenAt): self
    {
        $this->chosenAt = $chosenAt;

        return $this;
    }

    public function getChosenBy(): ?User
    {
        return $this->chosenBy;
    }

    public function setChosenBy(?User $chosenBy): self
    {
        $this->chosenBy = $chosenBy;

        return $this;
    }

    public function getModificationStatement(): ?ModificationStatement
    {
        return $this->modificationStatement;
    }

    public function getModification(): ?Modification
    {
        return $this->getModificationStatement()?->getModification();
    }

    public function setModificationStatement(?ModificationStatement $modificationStatement): self
    {
        // unset the owning side of the relation if necessary
        if ($modificationStatement === null && $this->modificationStatement !== null) {
            $this->modificationStatement->setChosen(null);
        }

        // set the owning side of the relation if necessary
        if ($modificationStatement !== null && $modificationStatement->getChosen() !== $this) {
            $modificationStatement->setChosen($this);
        }

        $this->modificationStatement = $modificationStatement;

        return $this;
    }
}
