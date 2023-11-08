<?php

namespace App\Security;

use App\Entity\Statement;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<Statement, string>
 */
class ExternalStatementVoter extends Voter
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

        // only vote on `Statement` objects
        if (!$subject instanceof Statement) {
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
        // you know $subject is a Post object, thanks to `supports()`
        /** @var Statement $statement */
        $statement = $subject;

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->canView($statement, $user),
            self::EDIT => $this->canEdit($statement, $user),
            self::OWN => $this->canOwn($statement, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Statement $statement, UserInterface $user): bool|null
    {
        // if they can edit, they can view
        if ($this->canEdit($statement, $user)) {
            return true;
        }

        return $statement->isPublic();
    }

    private function canEdit(Statement $statement, UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        // if they can own, they can edit
        if ($this->canOwn($statement, $user)) {
            return true;
        }

        return $statement->isPublic() && $statement->isEditable();
    }

    private function canOwn(Statement $statement, UserInterface $user): bool
    {
        // Owners can edit their own statement
        if ($statement->getOwners()->contains($user)) {
            return true;
        }

        // Admins can see everything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
