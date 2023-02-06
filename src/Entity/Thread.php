<?php

namespace App\Entity;

use App\Repository\ThreadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThreadRepository::class)]
class Thread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    /** @var Collection<Comment> */
    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: Comment::class)]
    protected Collection $comments;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\OneToOne(mappedBy: 'thread', cascade: ['persist', 'remove'])]
    private ?Discussion $discussion = null;

    public function __toString(): string
    {
        return $this->identifier;
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments->filter(
            fn (Comment $comment) => $comment->getDeletedAt() === null
        );
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setThread($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getThread() === $this) {
                $comment->setThread(null);
            }
        }

        return $this;
    }

    /**
     * @return ReadableCollection<Comment>
     */
    public function getTopLevelComments(bool $includeDeleted = false): ReadableCollection
    {
        return $this->comments->filter(
            fn (Comment $comment) => $comment->getParent() === null && ($includeDeleted || $comment->getDeletedAt() === null),
        );
    }

    /**
     * @return ReadableCollection<Comment>
     */
    public function getChildCommentsOf(Comment $parent, bool $includeDeleted = false): ReadableCollection
    {
        return $this->comments->filter(
            fn (Comment $comment) => $comment->getParent() !== null
                && $comment->getParent()->getId() === $parent->getId()
                && ($includeDeleted || $comment->getDeletedAt() === null)
        );
    }

    public function countChildCommentsRecursive(Comment $parent): int
    {
        $counter = 0;
        foreach ($this->getChildCommentsOf($parent) as $child) {
            ++$counter;
            $counter += $this->countChildCommentsRecursive($child);
        }

        return $counter;
    }

    public function getTopCommentsForPreview(): ReadableCollection
    {
        $array = $this->getComments()->toArray();
        usort($array, fn (Comment $a, Comment $b) => $b->getVoteScore() <=> $a->getVoteScore());

        return new ArrayCollection(array_slice($array, 0, 10));
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(Discussion $discussion): self
    {
        // set the owning side of the relation if necessary
        if ($discussion->getThread() !== $this) {
            $discussion->setThread($this);
        }

        $this->discussion = $discussion;

        return $this;
    }
}
