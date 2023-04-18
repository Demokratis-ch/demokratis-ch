<?php

namespace App\Entity;

use App\Repository\ModificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ModificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Modification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $justification = null;

    #[ORM\ManyToOne(inversedBy: 'modifications')]
    private ?User $createdBy = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'modifications')]
    #[ORM\JoinColumn(nullable: false)]
    private Paragraph $paragraph;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\OneToMany(mappedBy: 'modification', targetEntity: ModificationStatement::class)]
    private Collection $modificationStatements;

    public function __toString(): string
    {
        return strval($this->getId().'-'.$this->getCreatedBy());
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->modificationStatements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getJustification(): ?string
    {
        return $this->justification;
    }

    public function setJustification(?string $justification): self
    {
        $this->justification = $justification;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getParagraph(): Paragraph
    {
        return $this->paragraph;
    }

    public function setParagraph(Paragraph $paragraph): self
    {
        $this->paragraph = $paragraph;

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
     * @return Collection<int, ModificationStatement>
     */
    public function getModificationStatements(): Collection
    {
        return $this->modificationStatements;
    }

    public function addModificationStatement(ModificationStatement $modificationStatement): self
    {
        if (!$this->modificationStatements->contains($modificationStatement)) {
            $this->modificationStatements->add($modificationStatement);
            $modificationStatement->setModification($this);
        }

        return $this;
    }

    public function removeModificationStatement(ModificationStatement $modificationStatement): self
    {
        if ($this->modificationStatements->removeElement($modificationStatement)) {
            // set the owning side to null (unless already changed)
            if ($modificationStatement->getModification() === $this) {
                $modificationStatement->setModification(null);
            }
        }

        return $this;
    }
}
