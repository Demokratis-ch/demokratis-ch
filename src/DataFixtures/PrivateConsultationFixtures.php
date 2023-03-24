<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Consultation;
use App\Entity\Document;
use App\Entity\LegalText;
use App\Entity\Organisation;
use App\Entity\Paragraph;
use App\Entity\Statement;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PrivateConsultationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['dummy'];
    }

    public function getDependencies(): array
    {
        return [
            BasicFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(BasicFixtures::USER1);
        /** @var User $user2 */
        $user2 = $this->getReference(BasicFixtures::USER2);
        /** @var User $user3 */
        $user3 = $this->getReference(BasicFixtures::USER3);
        /** @var Organisation $organisation */
        $organisation = $this->getReference(BasicFixtures::ORGA1);

        $consultation = new Consultation();
        $consultation->setTitle('Private Vernehmlassung');
        $consultation->setDescription('Diese Vernehmlassung ist nur für Mitglieder der Organisation.');
        $consultation->setStatus('ongoing');
        $consultation->setFedlexId('proj/2021/17/cons_1');
        $consultation->setStartDate(new \DateTimeImmutable('2022-09-12 00:00:00.0'));
        $consultation->setEndDate(new \DateTimeImmutable('2022-12-16 00:00:00.0'));
        $consultation->setOffice('Unser Verein');
        $consultation->setOrganisation($organisation);

        $manager->persist($consultation);

        $document = new Document();
        $document->setConsultation($consultation);
        $document->setTitle('Vernehmlassungsvorlage');
        $document->setType('proposal');
        $document->setFedlexUri('https://fedlex.data.admin.ch/eli/dl/proj/2021/17/cons_1/doc_1');
        $document->setFilename('doc_1');
        $document->setImported('paragraphed');

        $manager->persist($document);

        $legalText = new LegalText();
        $legalText->setConsultation($consultation);
        $legalText->setImportedFrom($document);
        $legalText->setTitle('Bundesbeschluss über das Stimm- und Wahlrecht ab 16 Jahren');
        $legalText->setText(
            <<<TEXT
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec placerat nibh ex, nec suscipit ligula mollis at. Curabitur tincidunt tincidunt mi, vel tincidunt magna elementum ut. Sed hendrerit fermentum luctus. Nam non mi eget est mollis vestibulum a luctus nisl. Vestibulum sed posuere mauris. Nullam ex felis, consequat eu euismod at, lobortis eu lectus. Aenean urna elit, malesuada at ex accumsan, dictum bibendum turpis. Fusce lacus quam, lacinia gravida aliquet eget, imperdiet et libero. Aliquam convallis nisi et mauris finibus, nec ultrices velit auctor. In a laoreet augue, at blandit velit. Morbi id facilisis risus. Curabitur in orci dictum libero vehicula tempus non malesuada nulla. Nam sagittis magna et velit scelerisque, in commodo enim varius.
TEXT
        );

        $manager->persist($legalText);
        $manager->flush();

        for ($i = 0; $i < 10; ++$i) {
            $paragraph[$i] = new Paragraph();
            $paragraph[$i]->setPosition(200);
            $paragraph[$i]->setLegalText($legalText);
            $paragraph[$i]->setText('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec placerat nibh ex, nec suscipit ligula mollis at. Curabitur tincidunt tincidunt mi, vel tincidunt magna elementum ut. Sed hendrerit fermentum luctus.');

            $manager->persist($paragraph[$i]);
        }

        $statement = new Statement();
        $statement->setPublic(true);

        $statement->setOrganisation($organisation);
        $statement->setConsultation($consultation);
        $statement->setName('Statement');
        $manager->persist($statement);

        $manager->flush();
    }
}
