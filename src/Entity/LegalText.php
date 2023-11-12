<?php

namespace App\Entity;

use App\Entity\Traits\TimestampedEntityTrait;
use App\Repository\LegalTextRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LegalTextRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LegalText
{
    use TimestampedEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'legalTexts')]
    private ?User $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'legalText', targetEntity: Paragraph::class, cascade: ['persist', 'remove'])]
    private Collection $paragraphs;

    #[ORM\ManyToOne(inversedBy: 'legalTexts')]
    private ?Statement $statement = null;

    #[ORM\ManyToOne(inversedBy: 'legalTexts')]
    private ?Consultation $consultation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\OneToOne(inversedBy: 'legalText', cascade: ['persist', 'remove'])]
    private ?Document $importedFrom = null;

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function __construct()
    {
        $this->paragraphs = new ArrayCollection();
    }

    public static function create(
        Consultation $consultation,
        Document $importedFrom,
        string $title,
        string $text,
    ): self {
        $self = new self();
        $self->setConsultation($consultation);
        $self->setImportedFrom($importedFrom);
        $self->setTitle($title);
        $self->setText($text);
        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    /**
     * @return Collection<int, Paragraph>
     */
    public function getParagraphs(): Collection
    {
        return $this->paragraphs;
    }

    public function addParagraph(Paragraph $paragraph): self
    {
        if (!$this->paragraphs->contains($paragraph)) {
            $this->paragraphs->add($paragraph);
            $paragraph->setLegalText($this);
        }

        return $this;
    }

    public function removeParagraph(Paragraph $paragraph): self
    {
        if ($this->paragraphs->removeElement($paragraph)) {
            // set the owning side to null (unless already changed)
            if ($paragraph->getLegalText() === $this) {
                $paragraph->setLegalText(null);
            }
        }

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

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
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

    public function getImportedFrom(): ?Document
    {
        return $this->importedFrom;
    }

    public function setImportedFrom(?Document $importedFrom): self
    {
        $this->importedFrom = $importedFrom;

        return $this;
    }
}
