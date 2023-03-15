<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Organisation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class BasicFixtures extends Fixture implements FixtureGroupInterface
{
    public const USER1 = 'user1';
    public const USER2 = 'user2';
    public const USER3 = 'user3';
    public const ORGA1 = 'orga1';

    public static function getGroups(): array
    {
        return ['dummy'];
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('$2y$13$4v0Vaz3zlFegVjlTbhg7Oe7E/idxGhMAp899Ap1bz1ctGDJ3CE9Su');
        $user->setIsVerified(true);
        $manager->persist($user);
        $this->addReference(self::USER1, $user);

        $user2 = new User();
        $user2->setEmail('toast@test.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword('$2y$13$sg7vcNaaFm.q3Lh1YmHe0uiwCphhxJU4hjg84/vpsel3VjKfPmToy');
        $user2->setIsVerified(true);
        $manager->persist($user2);
        $this->addReference(self::USER2, $user2);

        $user3 = new User();
        $user3->setEmail('admin@test.com');
        $user3->setRoles(['ROLE_ADMIN']);
        $user3->setPassword('$2y$13$vhPJ/zh7XaFhO/NHeH7LGeL7Hr/FapN598IKarl7MHCx5DrZN6JD.');
        $user3->setIsVerified(true);
        $manager->persist($user3);
        $this->addReference(self::USER3, $user3);

        $organisation = new Organisation();
        $organisation->setName('Demokratis');
        $organisation->setSlug('demokratis');
        $organisation->setPublic(true);
        $organisation->setIsPersonalOrganisation(false);
        $manager->persist($organisation);
        $this->addReference(self::ORGA1, $organisation);

        $manager->flush();
    }
}
