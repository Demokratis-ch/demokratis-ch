<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use whatwedo\SearchBundle\Annotation\Index;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[UniqueEntity('fedlexId')]
#[ORM\HasLifecycleCallbacks]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Index]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $office = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(nullable: true)]
    private ?string $fedlexId = null;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: LegalText::class)]
    private Collection $legalTexts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $institution = null;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Document::class)]
    private Collection $documents;

    #[ORM\Column(length: 255, nullable: true)]
    #[Index]
    private ?string $humanTitle = null;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Statement::class)]
    private Collection $statements;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'consultations')]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Media::class)]
    private Collection $media;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Discussion::class)]
    private Collection $discussions;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: ExternalStatement::class)]
    private Collection $externalStatements;

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    public function __toString(): string
    {
        return substr($this->getTitle(), 0, 65).' ('.substr($this->getUuid(), 0, 8).')';
    }

    public function __construct()
    {
        $this->legalTexts = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->statements = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->externalStatements = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusColor(): string
    {
        return match ($this->getStatus()) {
            'ongoing' => 'teal',
            'planned' => 'blue',
            'done' => 'gray'
        };
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(?string $office): self
    {
        $this->office = $office;

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

    public function getUrlFedlexId(): ?string
    {
        return str_replace('/', '-', $this->fedlexId);
    }

    public function getFedlexId(): ?string
    {
        return $this->fedlexId;
    }

    public function setFedlexId(?string $fedlexId): self
    {
        $this->fedlexId = $fedlexId;

        return $this;
    }

    /**
     * @return Collection<int, LegalText>
     */
    public function getLegalTexts(): Collection
    {
        return $this->legalTexts;
    }

    public function addLegalText(LegalText $legalText): self
    {
        if (!$this->legalTexts->contains($legalText)) {
            $this->legalTexts->add($legalText);
            $legalText->setConsultation($this);
        }

        return $this;
    }

    public function removeLegalText(LegalText $legalText): self
    {
        if ($this->legalTexts->removeElement($legalText)) {
            // set the owning side to null (unless already changed)
            if ($legalText->getConsultation() === $this) {
                $legalText->setConsultation(null);
            }
        }

        return $this;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(?string $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setConsultation($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getConsultation() === $this) {
                $document->setConsultation(null);
            }
        }

        return $this;
    }

    public function getHumanTitle(): ?string
    {
        return $this->humanTitle;
    }

    public function setHumanTitle(?string $humanTitle): self
    {
        $this->humanTitle = $humanTitle;

        return $this;
    }

    /**
     * @return Collection<int, Statement>
     */
    public function getStatements(): Collection
    {
        return $this->statements;
    }

    public function addStatement(Statement $statement): self
    {
        if (!$this->statements->contains($statement)) {
            $this->statements->add($statement);
            $statement->setConsultation($this);
        }

        return $this;
    }

    public function removeStatement(Statement $statement): self
    {
        if ($this->statements->removeElement($statement)) {
            // set the owning side to null (unless already changed)
            if ($statement->getConsultation() === $this) {
                $statement->setConsultation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addConsultation($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeConsultation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setConsultation($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getConsultation() === $this) {
                $medium->setConsultation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setConsultation($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getConsultation() === $this) {
                $discussion->setConsultation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExternalStatement>
     */
    public function getExternalStatements(): Collection
    {
        return $this->externalStatements;
    }

    public function addExternalStatement(ExternalStatement $externalStatement): self
    {
        if (!$this->externalStatements->contains($externalStatement)) {
            $this->externalStatements->add($externalStatement);
            $externalStatement->setConsultation($this);
        }

        return $this;
    }

    public function removeExternalStatement(ExternalStatement $externalStatement): self
    {
        if ($this->externalStatements->removeElement($externalStatement)) {
            // set the owning side to null (unless already changed)
            if ($externalStatement->getConsultation() === $this) {
                $externalStatement->setConsultation(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
