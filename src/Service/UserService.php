<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function getLoggedInUser(): ?User
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
