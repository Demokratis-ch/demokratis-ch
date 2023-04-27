<?php

namespace App\Entity;

use App\Entity\Traits\TimestampedEntityTrait;
use App\Repository\FreeTextRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FreeTextRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FreeText
{
    use TimestampedEntityTrait;

    public const POSITION_BEFORE = 'before';
    public const POSITION_AFTER = 'after';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[ORM\ManyToOne(inversedBy: 'freeTexts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paragraph $paragraph = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Statement $statement = null;

    public function __toString(): string
    {
        return strval($this->getParagraph().'-'.$this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

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
}
