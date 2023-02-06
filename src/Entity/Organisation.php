<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: ExternalStatement::class)]
    private Collection $externalStatements;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column]
    private ?bool $public = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'organisations')]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Statement::class)]
    private Collection $statements;

    #[ORM\Column]
    private ?bool $isPersonalOrganisation = null;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: UserOrganisation::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Invite::class)]
    private Collection $invites;

    public function __construct()
    {
        $this->externalStatements = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->statements = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->invites = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $externalStatement->setOrganisation($this);
        }

        return $this;
    }

    public function removeExternalStatement(ExternalStatement $externalStatement): self
    {
        if ($this->externalStatements->removeElement($externalStatement)) {
            // set the owning side to null (unless already changed)
            if ($externalStatement->getOrganisation() === $this) {
                $externalStatement->setOrganisation(null);
            }
        }

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

    public function getDomain(): ?string
    {
        return preg_replace('(^https?://)', '', $this->getUrl());
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

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
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

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
            $statement->setOrganisation($this);
        }

        return $this;
    }

    public function removeStatement(Statement $statement): self
    {
        if ($this->statements->removeElement($statement)) {
            // set the owning side to null (unless already changed)
            if ($statement->getOrganisation() === $this) {
                $statement->setOrganisation(null);
            }
        }

        return $this;
    }

    public function isIsPersonalOrganisation(): ?bool
    {
        return $this->isPersonalOrganisation;
    }

    public function setIsPersonalOrganisation(bool $isPersonalOrganisation): self
    {
        $this->isPersonalOrganisation = $isPersonalOrganisation;

        return $this;
    }

    /**
     * @return Collection<int, UserOrganisation>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserOrganisation $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setOrganisation($this);
        }

        return $this;
    }

    public function removeUser(UserOrganisation $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getOrganisation() === $this) {
                $user->setOrganisation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invite>
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function addInvite(Invite $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites->add($invite);
            $invite->setOrganisation($this);
        }

        return $this;
    }

    public function removeInvite(Invite $invite): self
    {
        if ($this->invites->removeElement($invite)) {
            // set the owning side to null (unless already changed)
            if ($invite->getOrganisation() === $this) {
                $invite->setOrganisation(null);
            }
        }

        return $this;
    }
}
