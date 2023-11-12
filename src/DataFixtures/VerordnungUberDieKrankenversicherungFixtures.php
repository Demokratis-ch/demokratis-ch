<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Consultation;
use App\Entity\Document;
use App\Entity\LegalText;
use App\Entity\Organisation;
use App\Entity\Paragraph;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VerordnungUberDieKrankenversicherungFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['dummy'];
    }

    public function getDependencies(): array
    {
        return [
            BasicFixtures::class,
            FederalAndCantonsFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        /** @var Organisation $chFederationOrg */
        $chFederationOrg = $this->getReference(FederalAndCantonsFixtures::CONFEDERATION);

        $consultation = Consultation::create(
            'Änderung der Verordnung über die Krankenversicherung (KVV) und der Krankenpflege-Leistungsverordnung (KLV)',
            $chFederationOrg,
            'Organisationen der Apotheker und Apothekerinnen sowie der Zahnärzte und Zahnärztinnen sollen in der KVV eingeführt werden (inkl. KLV-Änderung). Die Vorgaben der KVV bezüglich Rechnungsstellung im Falle von Analysen, die Bestandteil eines Pauschaltarifs im ambulanten Bereich sind, sollen angepasst werden. Die Bestimmungen über den unterjährigen Wechsel sollen erweitert werden, sodass auch Versicherte mit Wahlfranchise unterjährige in die Versicherung mit eingeschränkter Wahl der Leistungserbringer wechseln können. Eine neue Verpflichtung der Versicherer wird bezüglich Meldepflicht des Ausgleichsbetrags festgelegt.',
            'ongoing',
            'proj/2023/73/cons_1',
            new \DateTimeImmutable('2023-10-18 00:00:00'),
            new \DateTimeImmutable('2024-02-01 00:00:00'),
            'Bundesrat',
        );

        $manager->persist($consultation);

        $document1 = Document::create(
            $consultation,
            'Vernehmlassungsvorlage',
            'proposal',
            'https://fedlex.data.admin.ch/eli/dl/proj/2023/73/cons_1/doc_1',
            'doc_1',
            'paragraphed',
        );
        $document2 = Document::create(
            $consultation,
            'Vernehmlassungsvorlage-2',
            'proposal',
            'https://fedlex.data.admin.ch/eli/dl/proj/2023/73/cons_1/doc_2',
            'doc_2',
            'paragraphed',
        );

        $manager->persist($document1);
        $manager->persist($document2);

        $legalText1 = $this->createLegalText(
            $consultation,
            $document1,
            'Verordnung über die Krankenversicherung',
            <<<TEXT
                I
                
                Die  Verordnung vom 27. Juni 1995
                1 über die Krankenversicherung wird wie folgt
                geändert:
                2. Abschnitt:
                Apotheker und Apothekerinnen sowie Organisationen der Apotheker
                und Apothekerinnen
                
                !!!
                Art. 40 Sachüberschrift
                Apotheker und Apothekerinnen
                
                !!!
                Art. 41  Organisationen der Apotheker und Apothekerinnen
                Organisationen der Apotheker und Apothekerinnen werden zugelassen, wenn sie die
                folgenden Voraussetzungen erfüllen:
                
                a. 
                  Sie sind nach der Gesetzgebung des Kantons, in dem sie tätig sind, zugelassen.
                
                b. 
                  Sie haben ihr en örtlichen, zeitlichen, sachlichen und personellen Tätigkeitsbereich festgelegt.
                
                c. 
                  Sie erbringen ihre Leistungen durch Personen, welche die Voraussetzungen
                nach Artikel  40 Absatz 1 Buchstabe a erfüllen.
                
                d. 
                  Sie verfügen über die für die Leistungserbringung not wendigen Einrichtungen.
                
                e. 
                  Sie weisen nach, dass sie die Qualitätsanforderungen nach Artikel  58 g erfüllen.

                !!!
                3. Abschnitt:
                Zahnärzte und Zahnärztinnen sowie Organisationen der Zahnärzte
                und Zahnärztinnen
                
                !!!
                Art. 42 Sachüberschrift
                Zahnärzte und Zahnärztinnen
                
                !!!
                Art. 43  Organisationen der Zahnärzte und Zahnärztinnen
                Organisationen der Zahnärzte und Zahnärztinnen werden für Leistungen nach Artikel  31 KVG zugelassen, wenn sie die folgenden Voraussetzungen erfüllen:
                
                a. 
                  Sie sind nach der Gesetzgebung des Kantons, in dem sie tätig sind, zugelassen.
                
                b. 
                  Sie haben ihren örtlichen, zeitlichen, sachlichen und personellen Tätigkeitsbereich festgelegt.
                
                c. 
                  Sie erbringen ihre Leistungen durch Personen, welche die Voraussetzungen
                nach Artikel  42 Buchstaben a und  b erfüllen.
                
                d. 
                  Sie verfügen über die für die Leistungserbringung notwendigen Einrichtungen.
                
                e. 
                  Sie weisen nach, dass sie die Qualitätsanforderungen nach Artikel  58 g erfüllen.
                
                !!!
                Art. 59 Abs. 3
                
                3 
                Bei Analysen erfolgt die Rechnungsstellung an den Schuldner der Vergütung aus -schliesslich durch das Laboratorium, das die Analyse durchgeführt hat. Pauschaltarife nach den Artikeln 43 Absätze 5 – 5quater  und 49 des Gesetzes bleiben vorbehalten.
                
                !!!
                Art. 100 Abs. 2
                
                2 
                Der Wechsel von einer Versicherung ohne eingeschränkte Wahl der Leistungserbringer in eine Versicherung mit ein geschränkter Wahl der Leistungserbringer ist je-derzeit möglich.
                
                !!!
                Art. 106c Abs. 1
                
                1 
                Der Versicherer teilt dem Kanton mit, ob er die Meldung einer bei ihm versicherten Person zuordnen kann. Er teilt dem Kanton ausserdem die genehmigte Prämie sowie den Ausgleichsbetrag nach Artikel 26 Absatz 4 der Krankenversicherungsaufsichtsverordnung vom  18. November 20152  mit.
                
                !!!
                II
                
                Übergangsbestimmung zur Änderung vom …
                ….
                
                !!!
                III
                
                Diese Verordnung tritt am 1. Juli 2024 in Kraft.
            TEXT
        );

        $manager->persist($legalText1);

        $legalText2 = $this->createLegalText(
            $consultation,
            $document2,
            'Verordnung  des EDI über Leistungen in der obligatorischen Krankenpflegeversicherung (Krankenpflege-Leistungsverordnung, KLV)',
            <<<TEXT
                Das Eidgenössische Departement des Innern (EDI)
                verordnet:
                
                
                I
                
                Die Verordnung des EDI vom 29. September 1995
                1  über Leistungen in der obligatorischen Krankenpflegeversicherung wird wie folgt geändert:
                
                
                Art. 4 a Abs. 1 Einleitungssatz
                
                1 
                Die Versicherung übernimmt  die Kosten folgende r Leistungen , die von nach
                Artikel  40  Absatz 1 KVV zugelassenen Apothekern und Apothekerinnen oder von
                nach Artikel  41  KVV zugelassenen Organisationen der Apotheker und Apotheker innen  erbracht werden :
                
                !!!
                II
                
                Übergangsbestimmungen zur Änderung vom …
                
                !!!
                III
                
                Diese Verordnung tritt am 1. Juli 2024 in Kraft.
            TEXT
        );

        $manager->persist($legalText2);

        $manager->flush();
    }

    private function createLegalText(Consultation $consultation, Document $document, string $title, string $text): LegalText
    {
        $legalText = LegalText::create($consultation, $document, $title, $text);

        $position = 0;
        foreach (explode('!!!', $text) as $paragraphText) {
            $paragraphText = trim($paragraphText);
            $paragraph = Paragraph::create($legalText, ++$position, $paragraphText);
            $legalText->addParagraph($paragraph);
        }

        return $legalText;
    }
}
