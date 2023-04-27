<?php

namespace App\Entity;

use App\Entity\Traits\TimestampedEntityTrait;
use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Document
{
    use TimestampedEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filepath = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Consultation $consultation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fedlexUri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imported = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\OneToOne(mappedBy: 'importedFrom', cascade: ['persist', 'remove'])]
    private ?LegalText $legalText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localFilename = null;

    public function __toString(): string
    {
        return strval($this->getId().'-'.$this->getType().'-'.$this->getLocalFilename());
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFedlexFilepath(): ?string
    {
        return 'https://www.fedlex.admin.ch/filestore/fedlex.data.admin.ch/eli/dl/'.$this->getConsultation()->getFedlexId().'/'.$this->getFilename().'/de/pdf-a/fedlex-data-admin-ch-eli-dl-'.$this->getConsultation()->getUrlFedlexId().'-'.$this->getFilename().'-de-pdf-a.pdf';
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(?string $filepath): self
    {
        $this->filepath = $filepath;

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

    public function getFedlexUri(): ?string
    {
        return $this->fedlexUri;
    }

    public function setFedlexUri(?string $fedlexUri): self
    {
        $this->fedlexUri = $fedlexUri;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getImported(): ?string
    {
        return $this->imported;
    }

    public function setImported(?string $imported): self
    {
        $this->imported = $imported;

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

    public function getLegalText(): ?LegalText
    {
        return $this->legalText;
    }

    public function setLegalText(?LegalText $legalText): self
    {
        // unset the owning side of the relation if necessary
        if ($legalText === null && $this->legalText !== null) {
            $this->legalText->setImportedFrom(null);
        }

        // set the owning side of the relation if necessary
        if ($legalText !== null && $legalText->getImportedFrom() !== $this) {
            $legalText->setImportedFrom($this);
        }

        $this->legalText = $legalText;

        return $this;
    }

    public function getLocalFilename(): ?string
    {
        return $this->localFilename;
    }

    public function setLocalFilename(?string $localFilename): self
    {
        $this->localFilename = $localFilename;

        return $this;
    }
}
