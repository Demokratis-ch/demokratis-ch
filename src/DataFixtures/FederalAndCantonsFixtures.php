<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Organisation;
use App\Enums\Cantons;
use App\Enums\Organisations;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class FederalAndCantonsFixtures extends Fixture implements FixtureGroupInterface
{
    public const CONFEDERATION = 'confederation';

    public static function getGroups(): array
    {
        return ['dummy', 'cantons'];
    }

    public function load(ObjectManager $manager): void
    {
        $confederation = new Organisation();
        $confederation->setName('Schweizerische Eidgenossenschaft');
        $confederation->setSlug('CH');
        $confederation->setDescription('Schweizerische Eidgenossenschaft');
        $confederation->setUrl('https://www.admin.ch');
        $confederation->setPublic(true);
        $confederation->setIsPersonalOrganisation(false);
        $confederation->setType(Organisations::FEDERAL->value);
        $manager->persist($confederation);

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

        $this->addReference(self::CONFEDERATION, $confederation);
    }
}
