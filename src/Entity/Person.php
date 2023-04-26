<?php

namespace App\Entity;

use App\Entity\Trait\TimestampedEntityTrait;
use App\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    use TimestampedEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\OneToOne(inversedBy: 'person', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'person', cascade: ['persist', 'remove'])]
    private ?Invite $invite = null;

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function getDisplayName(): string
    {
        if ($this->getFirstname() && $this->getLastname()) {
            return $this->getFirstname().' '.$this->getLastname();
        } elseif ($this->getFirstname()) {
            return $this->getFirstname();
        } elseif ($this->getLastname()) {
            return $this->getLastname();
        }
        throw new \RuntimeException('Person has no name.');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getInvite(): ?Invite
    {
        return $this->invite;
    }

    public function setInvite(?Invite $invite): self
    {
        // unset the owning side of the relation if necessary
        if ($invite === null && $this->invite !== null) {
            $this->invite->setPerson(null);
        }

        // set the owning side of the relation if necessary
        if ($invite !== null && $invite->getPerson() !== $this) {
            $invite->setPerson($this);
        }

        $this->invite = $invite;

        return $this;
    }
}
