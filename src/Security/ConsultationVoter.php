<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Consultation;
use App\Entity\User;
use App\Entity\UserOrganisation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConsultationVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Consultation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Consultation $consultation */
        $consultation = $subject;

        if ($consultation->getOrganisation() === null) {
            return true;
        }

        if ($consultation->getOrganisation()->getType() === 'canton') {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($user->getOrganisations()
            ->map(fn (UserOrganisation $userOrganisation) => $userOrganisation->getOrganisation()->getId())
            ->contains($consultation->getOrganisation()->getId())
        ) {
            return true;
        }

        return false;
    }
}
