<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:list', 'user:item'])]
    private $id;

    #[ORM\Column(type: 'uuid')]
    private $uuid;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:list', 'user:item'])]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Person $person = null;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: LegalText::class)]
    private Collection $legalTexts;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Modification::class)]
    private Collection $modifications;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Statement::class, mappedBy: 'owners')]
    private Collection $owningStatements;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Vote::class)]
    private Collection $votes;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Tag::class)]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'chosenBy', targetEntity: ChosenModification::class)]
    private Collection $chosenModifications;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Media::class)]
    private Collection $media;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Discussion::class)]
    private Collection $discussions;

    #[ORM\ManyToMany(targetEntity: Statement::class, mappedBy: 'approvedBy')]
    private Collection $approvedStatements;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: ExternalStatement::class)]
    private Collection $externalStatements;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Organisation $activeOrganisation = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserOrganisation::class)]
    private Collection $organisations;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Membership::class)]
    private Collection $memberships;

    public function __construct()
    {
        $this->legalTexts = new ArrayCollection();
        $this->modifications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->owningStatements = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->chosenModifications = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->approvedStatements = new ArrayCollection();
        $this->externalStatements = new ArrayCollection();
        $this->organisations = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->getPerson()) {
            return $this->getPerson();
        } else {
            return $this->getEmail();
        }
    }

    public function getName(): string|null
    {
        if ($this->getPerson()) {
            return $this->getPerson()->getDisplayName();
        } else {
            return $this->getEmail();
        }
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

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return void
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        // unset the owning side of the relation if necessary
        if ($person === null && $this->person !== null) {
            $this->person->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($person !== null && $person->getUser() !== $this) {
            $person->setUser($this);
        }

        $this->person = $person;

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
            $legalText->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLegalText(LegalText $legalText): self
    {
        if ($this->legalTexts->removeElement($legalText)) {
            // set the owning side to null (unless already changed)
            if ($legalText->getCreatedBy() === $this) {
                $legalText->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Modification>
     */
    public function getModifications(): Collection
    {
        return $this->modifications;
    }

    public function addModification(Modification $modification): self
    {
        if (!$this->modifications->contains($modification)) {
            $this->modifications->add($modification);
            $modification->setCreatedBy($this);
        }

        return $this;
    }

    public function removeModification(Modification $modification): self
    {
        if ($this->modifications->removeElement($modification)) {
            // set the owning side to null (unless already changed)
            if ($modification->getCreatedBy() === $this) {
                $modification->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Statement>
     */
    public function getOwningStatements(): Collection
    {
        return $this->owningStatements;
    }

    public function addOwningStatement(Statement $owningStatement): self
    {
        if (!$this->owningStatements->contains($owningStatement)) {
            $this->owningStatements->add($owningStatement);
            $owningStatement->addOwner($this);
        }

        return $this;
    }

    public function removeOwningStatement(Statement $owningStatement): self
    {
        if ($this->owningStatements->removeElement($owningStatement)) {
            $owningStatement->removeOwner($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setAuthor($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getAuthor() === $this) {
                $vote->setAuthor(null);
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
            $tag->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getCreatedBy() === $this) {
                $tag->setCreatedBy(null);
            }
        }

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
            $chosenModification->setChosenBy($this);
        }

        return $this;
    }

    public function removeChosenModification(ChosenModification $chosenModification): self
    {
        if ($this->chosenModifications->removeElement($chosenModification)) {
            // set the owning side to null (unless already changed)
            if ($chosenModification->getChosenBy() === $this) {
                $chosenModification->setChosenBy(null);
            }
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
            $medium->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getCreatedBy() === $this) {
                $medium->setCreatedBy(null);
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
            $discussion->setCreatedBy($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getCreatedBy() === $this) {
                $discussion->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Statement>
     */
    public function getApprovedStatements(): Collection
    {
        return $this->approvedStatements;
    }

    public function addApprovedStatement(Statement $approvedStatement): self
    {
        if (!$this->approvedStatements->contains($approvedStatement)) {
            $this->approvedStatements->add($approvedStatement);
            $approvedStatement->addApprovedBy($this);
        }

        return $this;
    }

    public function removeApprovedStatement(Statement $approvedStatement): self
    {
        if ($this->approvedStatements->removeElement($approvedStatement)) {
            $approvedStatement->removeApprovedBy($this);
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
            $externalStatement->setCreatedBy($this);
        }

        return $this;
    }

    public function removeExternalStatement(ExternalStatement $externalStatement): self
    {
        if ($this->externalStatements->removeElement($externalStatement)) {
            // set the owning side to null (unless already changed)
            if ($externalStatement->getCreatedBy() === $this) {
                $externalStatement->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getActiveOrganisation(): ?Organisation
    {
        return $this->activeOrganisation;
    }

    public function setActiveOrganisation(?Organisation $activeOrganisation): self
    {
        $this->activeOrganisation = $activeOrganisation;

        return $this;
    }

    /**
     * @return Collection<int, UserOrganisation>
     */
    public function getOrganisations(): Collection
    {
        return $this->organisations;
    }

    public function addOrganisation(UserOrganisation $organisation): self
    {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations->add($organisation);
            $organisation->setUser($this);
        }

        return $this;
    }

    public function removeOrganisation(UserOrganisation $organisation): self
    {
        if ($this->organisations->removeElement($organisation)) {
            // set the owning side to null (unless already changed)
            if ($organisation->getUser() === $this) {
                $organisation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Membership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Membership $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
            $membership->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMembership(Membership $membership): self
    {
        if ($this->memberships->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getCreatedBy() === $this) {
                $membership->setCreatedBy(null);
            }
        }

        return $this;
    }
}
