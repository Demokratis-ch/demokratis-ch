<?php

namespace App\Entity;

use App\Repository\StatementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StatementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Statement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $justification = null;

    #[ORM\OneToMany(mappedBy: 'statement', targetEntity: LegalText::class)]
    private Collection $legalTexts;

    #[ORM\ManyToOne(inversedBy: 'statements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'owningStatements')]
    private Collection $owners;

    #[ORM\Column]
    private bool $public = false;

    #[ORM\Column]
    private bool $editable = false;

    #[ORM\OneToMany(mappedBy: 'statement', targetEntity: ChosenModification::class)]
    private Collection $chosenModifications;

    #[ORM\OneToMany(mappedBy: 'statement', targetEntity: ModificationStatement::class)]
    private Collection $modificationStatements;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intro = null;

    #[JoinTable(name: 'approved_statements')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'approvedStatements')]
    private Collection $approvedBy;

    #[ORM\ManyToOne(inversedBy: 'statements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organisation $organisation = null;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function __construct()
    {
        $this->legalTexts = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->chosenModifications = new ArrayCollection();
        $this->modificationStatements = new ArrayCollection();
        $this->approvedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
            $legalText->setStatement($this);
        }

        return $this;
    }

    public function removeLegalText(LegalText $legalText): self
    {
        if ($this->legalTexts->removeElement($legalText)) {
            // set the owning side to null (unless already changed)
            if ($legalText->getStatement() === $this) {
                $legalText->setStatement(null);
            }
        }

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
     * @return Collection<int, User>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
        }

        return $this;
    }

    public function removeOwner(User $owner): self
    {
        $this->owners->removeElement($owner);

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function isEditable(): ?bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): self
    {
        $this->editable = $editable;

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
            $chosenModification->setStatement($this);
        }

        return $this;
    }

    public function removeChosenModification(ChosenModification $chosenModification): self
    {
        if ($this->chosenModifications->removeElement($chosenModification)) {
            // set the owning side to null (unless already changed)
            if ($chosenModification->getStatement() === $this) {
                $chosenModification->setStatement(null);
            }
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
            $modificationStatement->setStatement($this);
        }

        return $this;
    }

    public function removeModificationStatement(ModificationStatement $modificationStatement): self
    {
        if ($this->modificationStatements->removeElement($modificationStatement)) {
            // set the owning side to null (unless already changed)
            if ($modificationStatement->getStatement() === $this) {
                $modificationStatement->setStatement(null);
            }
        }

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getApprovedBy(): Collection
    {
        return $this->approvedBy;
    }

    public function addApprovedBy(User $approvedBy): self
    {
        if (!$this->approvedBy->contains($approvedBy)) {
            $this->approvedBy->add($approvedBy);
        }

        return $this;
    }

    public function removeApprovedBy(User $approvedBy): self
    {
        $this->approvedBy->removeElement($approvedBy);

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }
}
