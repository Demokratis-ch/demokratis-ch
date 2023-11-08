<?php

namespace App\Security;

use App\Entity\Comment;
use App\Repository\VoteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<Comment, string>
 */
class VoteCommentVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const VIEW = 'view';
    public const VOTE = 'vote';
    public const OWN = 'own';

    private Security $security;
    private VoteRepository $voteRepository;

    public function __construct(Security $security, VoteRepository $voteRepository)
    {
        $this->voteRepository = $voteRepository;
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VOTE, self::OWN])) {
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
            // the user is not authenticated and not able to vote
            return false;
        }
        // you know $subject is a Post object, thanks to `supports()`
        /** @var Comment $statement */
        $comment = $subject;

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->canView($comment, $user),
            self::VOTE => $this->canVote($comment, $user),
            self::OWN => $this->canOwn($comment, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Comment $comment, UserInterface $user): bool
    {
        // if they can vote, they can view
        if ($this->canVote($comment, $user)) {
            return true;
        }

        // Everyone can see votes
        return true;
    }

    private function canVote(Comment $comment, UserInterface $user): bool
    {
        // Everyone can vote only once per comment
        if (count($this->voteRepository->findBy(['comment' => $comment, 'author' => $user])) > 0) {
            return false;
        } else {
            return true;
        }
    }

    private function canOwn(Comment $comment, UserInterface $user): bool
    {
        // Super admins own everything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
