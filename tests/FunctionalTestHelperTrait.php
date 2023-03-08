<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait FunctionalTestHelperTrait
{
    protected static function setUpClientWithFixtures(array $fixtureProviders): KernelBrowser
    {
        $client = static::createClient();
        /** @var EntityManagerInterface $em */
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        $loader = new Loader();
        foreach ($fixtureProviders as $fixtureProvider) {
            $loader->addFixture($fixtureProvider);
        }
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        return $client;
    }

    abstract protected static function createClient(): KernelBrowser;
}
