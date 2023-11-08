<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<Comment, string>
 */
class CommentVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const OWN = 'own';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::OWN])) {
            return false;
        }

        // only vote on `Comment` objects
        if (!$subject instanceof Comment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            // the user is not authenticated, e.g. only allow them to
            // see public posts
            return $subject->isPublic();
        }
        // you know $subject is a Comment object, thanks to `supports()`
        /** @var Comment $comment */
        $comment = $subject;

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->canView($comment, $user),
            self::EDIT => $this->canEdit($comment, $user),
            self::OWN => $this->canOwn($comment, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Comment $comment, UserInterface $user): bool|null
    {
        // if they can edit, they can view
        if ($this->canEdit($comment, $user)) {
            return true;
        }

        if ($comment->getDeletedAt() !== null) {
            return false;
        } else {
            return true;
        }
    }

    private function canEdit(Comment $comment, UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->canOwn($comment, $user);
    }

    private function canOwn(Comment $comment, UserInterface $user): bool
    {
        // Owners own their own comments
        if ($comment->getAuthor() === $user) {
            return true;
        }
        // Admins can see everything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
