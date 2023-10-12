<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Organisation;
use App\Enums\Cantons;
use App\Enums\Organisations;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CantonsFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['dummy', 'cantons'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (Cantons::cases() as $key => $canton) {
            $organisation[$canton->value] = new Organisation();
            $organisation[$canton->value]->setName($canton->value);
            $organisation[$canton->value]->setSlug($canton->name);
            $organisation[$canton->value]->setDescription('Kanton '.$canton->value);
            $organisation[$canton->value]->setUrl('https://'.strtolower($canton->name).'.ch');
            $organisation[$canton->value]->setPublic(true);
            $organisation[$canton->value]->setIsPersonalOrganisation(false);
            $organisation[$canton->value]->setType(Organisations::CANTON->value);
            $manager->persist($organisation[$canton->value]);
        }

        $manager->flush();
    }
}
