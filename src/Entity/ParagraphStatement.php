<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimestampedEntityTrait;
use App\Repository\ModificationStatementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModificationStatementRepository::class)]
class ParagraphStatement
{
    use TimestampedEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paragraph $paragraph = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statement $statement = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Thread $thread = null;

    public static function create(Statement $statement, Paragraph $paragraph, Thread $thread = null)
    {
        $self = new self();
        $self->setStatement($statement);
        $self->setParagraph($paragraph);
        $self->setThread($thread);

        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getParagraph(): ?Paragraph
    {
        return $this->paragraph;
    }

    public function setParagraph(?Paragraph $paragraph): void
    {
        $this->paragraph = $paragraph;
    }

    public function getStatement(): ?Statement
    {
        return $this->statement;
    }

    public function setStatement(?Statement $statement): void
    {
        $this->statement = $statement;
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
