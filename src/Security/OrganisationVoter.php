<?php

namespace App\Security;

use App\Entity\Organisation;
use App\Entity\User;
use App\Repository\UserOrganisationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrganisationVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const OWN = 'own';

    private Security $security;
    private UserOrganisationRepository $userOrganisationRepository;

    public function __construct(Security $security, UserOrganisationRepository $userOrganisationRepository)
    {
        $this->security = $security;
        $this->userOrganisationRepository = $userOrganisationRepository;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::OWN])) {
            return false;
        }

        // only vote on `Organisation` objects
        if (!$subject instanceof Organisation) {
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

        /** @var Organisation $organisation */
        $organisation = $subject;

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->canView($organisation, $user),
            self::EDIT => $this->canEdit($organisation, $user),
            self::OWN => $this->canOwn($organisation, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Organisation $organisation, UserInterface $user): bool|null
    {
        // if they can edit, they can view
        if ($this->canEdit($organisation, $user)) {
            return true;
        }

        return $organisation->isPublic();
    }

    private function canEdit(Organisation $organisation, UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->canOwn($organisation, $user);
    }

    private function canOwn(Organisation $organisation, UserInterface $user): bool
    {
        // Owners can edit their own organisation
        $owner = $this->userOrganisationRepository->findOneBy(['organisation' => $organisation, 'user' => $user, 'isAdmin' => true]);

        if ($owner) {
            return true;
        }

        // Platform admins can see everything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
