<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Organisation;
use App\Enums\Organisations;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CantonsFixtures extends Fixture implements FixtureGroupInterface
{
    public const CANTONS = [
        ['AG' => 'Aargau'],
        ['AI' => 'Appenzell Innerrhoden'],
        ['AR' => 'Appenzell Ausserrhoden'],
        ['BE' => 'Bern'],
        ['BL' => 'Basel-Landschaft'],
        ['BS' => 'Basel-Stadt'],
        ['FR' => 'Freiburg'],
        ['GE' => 'Genf'],
        ['GL' => 'Glarus'],
        ['GR' => 'Graubünden'],
        ['JU' => 'Jura'],
        ['LU' => 'Luzern'],
        ['NE' => 'Neuenburg'],
        ['NW' => 'Nidwalden'],
        ['OW' => 'Obwalden'],
        ['SG' => 'St. Gallen'],
        ['SH' => 'Schaffhausen'],
        ['SO' => 'Solothurn'],
        ['SZ' => 'Schwyz'],
        ['TG' => 'Thurgau'],
        ['TI' => 'Tessin'],
        ['UR' => 'Uri'],
        ['VD' => 'Waadt'],
        ['VS' => 'Wallis'],
        ['ZG' => 'Zug'],
        ['ZH' => 'Zürich'],
    ];

    public static function getGroups(): array
    {
        return ['dummy', 'cantons'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::CANTONS as $key => $value) {
            foreach ($value as $slug => $name) {

                $organisation[$slug] = new Organisation();
                $organisation[$slug]->setName($name);
                $organisation[$slug]->setSlug($slug);
                $organisation[$slug]->setDescription('Kanton '.$name);
                $organisation[$slug]->setUrl('https://'.strtolower($slug).'.ch');
                $organisation[$slug]->setPublic(true);
                $organisation[$slug]->setIsPersonalOrganisation(false);
                $organisation[$slug]->setType(Organisations::CANTON->value);
                $manager->persist($organisation[$slug]);
            }
        }

        $manager->flush();
    }
}
