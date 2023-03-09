<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Service\TaggingService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $tags = TaggingService::TAGS;

        foreach ($tags as $i => $tag) {
            $entities[$i] = new Tag();
            $entities[$i]->setName(ucfirst($tag));
            $entities[$i]->setSlug($tag);
            $entities[$i]->setCreatedAt(new \DateTimeImmutable());
            $entities[$i]->setCreatedBy(null);
            $entities[$i]->setApproved(true);
            $manager->persist($entities[$i]);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dummy', 'tags'];
    }
}
