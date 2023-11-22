<?php

namespace App\Entity;

use App\Entity\Traits\TimestampedEntityTrait;
use App\Repository\ModificationStatementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ModificationStatementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ModificationStatement
{
    use TimestampedEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'modificationStatements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modification $modification = null;

    #[ORM\ManyToOne(inversedBy: 'modificationStatements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statement $statement = null;

    #[ORM\Column]
    private ?bool $refused = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $decisionReason = null;

    #[ORM\OneToOne(mappedBy: 'modificationStatement', cascade: ['persist', 'remove'])]
    private ?ChosenModification $chosen = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Thread $thread = null;

    public function __toString(): string
    {
        return strval($this->getId());
    }

    public static function create(
        Statement $statement,
        Modification $modification,
    ): self {
        $self = new self();
        $self->setStatement($statement);
        $self->setModification($modification);

        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModification(): ?Modification
    {
        return $this->modification;
    }

    public function setModification(?Modification $modification): self
    {
        $this->modification = $modification;

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

    public function isRefused(): ?bool
    {
        return $this->refused;
    }

    public function setRefused(bool $refused): self
    {
        $this->refused = $refused;

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

    public function getDecisionReason(): ?string
    {
        return $this->decisionReason;
    }

    public function setDecisionReason(?string $decisionReason): self
    {
        $this->decisionReason = $decisionReason;

        return $this;
    }

    public function getChosen(): ?ChosenModification
    {
        return $this->chosen;
    }

    public function setChosen(?ChosenModification $chosen): self
    {
        $this->chosen = $chosen;

        return $this;
    }

    public function getThreadOrCreate(): Thread
    {
        if ($this->thread === null) {
            $this->thread = new Thread();
        }

        return $this->thread;
    }

    public function setThread(?Thread $thread): void
    {
        $this->thread = $thread;
    }
}
