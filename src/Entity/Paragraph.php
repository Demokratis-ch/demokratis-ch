<?php

namespace App\Entity;

use App\Repository\ParagraphRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ParagraphRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Paragraph
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $text = null;

    #[ORM\Column(nullable: false)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'paragraphs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LegalText $legalText = null;

    #[ORM\OneToMany(mappedBy: 'paragraph', targetEntity: Modification::class)]
    private Collection $modifications;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\OneToMany(mappedBy: 'paragraph', targetEntity: ChosenModification::class)]
    private Collection $chosenModifications;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\OneToMany(mappedBy: 'paragraph', targetEntity: FreeText::class, orphanRemoval: true)]
    private Collection $freeTexts;

    public function __toString(): string
    {
        return strval($this->getId());
    }

    public function __construct()
    {
        $this->modifications = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->chosenModifications = new ArrayCollection();
        $this->freeTexts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLegalText(): ?LegalText
    {
        return $this->legalText;
    }

    public function setLegalText(?LegalText $legalText): self
    {
        $this->legalText = $legalText;

        return $this;
    }

    /**
     * @return Collection<int, Modification>
     */
    public function getModifications(): Collection
    {
        return $this->modifications;
    }

    public function addModification(Modification $modification): self
    {
        if (!$this->modifications->contains($modification)) {
            $this->modifications->add($modification);
            $modification->setParagraph($this);
        }

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    #[ORM\PrePersist]
    public function setUuid(): self
    {
        if ($this->getUuid() === null) {
            $this->uuid = Uuid::v4();
        }

        return $this;
    }

    /**
     * @return Collection<int, ChosenModification>
     */
    public function getChosenModifications(): Collection
    {
        return $this->chosenModifications;
    }

    public function addChosenModification(ChosenModification $chosenModification): self
    {
        if (!$this->chosenModifications->contains($chosenModification)) {
            $this->chosenModifications->add($chosenModification);
            $chosenModification->setParagraph($this);
        }

        return $this;
    }

    public function removeChosenModification(ChosenModification $chosenModification): self
    {
        if ($this->chosenModifications->removeElement($chosenModification)) {
            // set the owning side to null (unless already changed)
            if ($chosenModification->getParagraph() === $this) {
                $chosenModification->setParagraph(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, FreeText>
     */
    public function getFreeTexts(): Collection
    {
        return $this->freeTexts;
    }

    public function addFreeText(FreeText $freeText): self
    {
        if (!$this->freeTexts->contains($freeText)) {
            $this->freeTexts->add($freeText);
            $freeText->setParagraph($this);
        }

        return $this;
    }

    public function removeFreeText(FreeText $freeText): self
    {
        if ($this->freeTexts->removeElement($freeText)) {
            // set the owning side to null (unless already changed)
            if ($freeText->getParagraph() === $this) {
                $freeText->setParagraph(null);
            }
        }

        return $this;
    }
}
