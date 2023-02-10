<?php

namespace App\DataFixtures;

use App\Entity\ChosenModification;
use App\Entity\Comment;
use App\Entity\Consultation;
use App\Entity\Document;
use App\Entity\LegalText;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Paragraph;
use App\Entity\Statement;
use App\Entity\Thread;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ConsultationStimmUndWahlrecht16JaehrigeFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrganisationRepository $organisationRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // user 1 must exist. created by the init.sh
        $user = $this->userRepository->find(1);
        $user2 = $this->userRepository->find(2);
        $user3 = $this->userRepository->find(3);

        $consultation = new Consultation();
        $consultation->setTitle('Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');
        $consultation->setDescription('Die Kommission schlägt vor, die Bundesverfassung so zu ändern, dass das aktive Stimm- und Wahlrechtsalter von 18 auf 16 Jahre gesenkt wird. Das Mindestalter für die Wählbarkeit in politische Ämter und an das Bundesgericht soll bei 18 Altersjahren belassen werden.');
        $consultation->setStatus('ongoing');
        $consultation->setFedlexId('proj/2022/59/cons_1');
        $consultation->setStartDate(new \DateTimeImmutable('2022-09-12 00:00:00.0'));
        $consultation->setEndDate(new \DateTimeImmutable('2022-12-16 00:00:00.0'));
        $consultation->setOffice('Parlamentarische Kommissionen');

        $manager->persist($consultation);

        $document = new Document();
        $document->setConsultation($consultation);
        $document->setTitle('Vernehmlassungsvorlage');
        $document->setType('proposal');
        $document->setFedlexUri('https://fedlex.data.admin.ch/eli/dl/proj/2022/59/cons_1/doc_1');
        $document->setFilename('doc_1');
        $document->setImported('paragraphed');

        $manager->persist($document);

        $legalText = new LegalText();
        $legalText->setConsultation($consultation);
        $legalText->setImportedFrom($document);
        $legalText->setTitle('Bundesbeschluss über das Stimm- und Wahlrecht ab 16 Jahren');
        $legalText->setText(
            <<<TEXT
I

Die Bundesverfassung wird wie folgt geändert:

Art. 136 Abs. 1

1 
Die politischen Rechte in Bundessachen stehen allen Schweizerinnen und Schweizern zu, die das 16. Altersjahr zurückgelegt haben und die nicht wegen Geisteskrankheit oder Geistesschwäche entmündigt sind. Alle haben die gleichen politischen Rechte und Pflichten.

Art. 143
In den Nationalrat, in den Bundesrat und in das Bundesgericht sind alle Stimmberechtigten wählbar, die das 18. Altersjahr zurückgelegt haben.
!!!
II

1 
Dieser Beschluss untersteht der Abstimmung des Volkes und der Stände.
TEXT
        );

        $manager->persist($legalText);
        $manager->flush();

        $paragraph1 = new Paragraph();
        $paragraph1->setPosition(100);
        $paragraph1->setLegalText($legalText);
        $paragraph1->setText(
            <<<TEXT
I
Die Bundesverfassung wird wie folgt geändert:

Art. 136 Abs. 1

1 
Die politischen Rechte in Bundessachen stehen allen Schweizerinnen und Schweizern zu, die das 16. Altersjahr zurückgelegt haben und die nicht wegen Geisteskrankheit oder Geistesschwäche entmündigt sind. Alle haben die gleichen politischen Rechte und Pflichten.

Art. 143
In den Nationalrat, in den Bundesrat und in das Bundesgericht sind alle Stimmberechtigten wählbar, die das 18. Altersjahr zurückgelegt haben.

TEXT
        );

        $manager->persist($paragraph1);

        $paragraph2 = new Paragraph();
        $paragraph2->setPosition(200);
        $paragraph2->setLegalText($legalText);
        $paragraph2->setText(
            <<<TEXT
II
1 
Dieser Beschluss untersteht der Abstimmung des Volkes und der Stände.

TEXT
        );

        $manager->persist($paragraph2);

        $i = 0;
        while ($i <= 30) {
            ++$i;

            $paragraph[$i] = new Paragraph();
            $paragraph[$i]->setPosition(200);
            $paragraph[$i]->setLegalText($legalText);
            $paragraph[$i]->setText(
                <<<TEXT
II

1 
Dieser Beschluss mit Nummer "**$i**" untersteht der Abstimmung des Volkes und der Stände.

TEXT
            );

            $manager->persist($paragraph[$i]);
        }

        $statement = new Statement();
        $statement->setPublic(true);

        $organisation = $this->organisationRepository->findOneBy(['name' => 'Demokratis']);

        $statement->setOrganisation($organisation);
        $statement->setConsultation($consultation);
        $statement->setName('Meine Meinung');
        $manager->persist($statement);

        $manager->flush();

        $modification = new Modification();
        $modification->setCreatedBy($user);
        $modification->setText(
            <<<TEXT
I

Die Bundesverfassung wird wie folgt geändert:

Art. 136 Abs. 1

1 
Die politischen Rechte in Bundessachen stehen allen Schweizer*n zu, die das 16. Altersjahr zurückgelegt haben und die nicht wegen Geisteskrankheit, Geistesschwäche oder anderen Gründen entmündigt sind. Alle haben die gleichen politischen Rechte und Pflichten.

Art. 143
In den Nationalrat, in den Bundesrat und in das Bundesgericht sind alle Stimmberechtigten wählbar, die das 18. Altersjahr zurückgelegt haben.

TEXT
        );

        $modification->setParagraph($paragraph1);
        $manager->persist($modification);

        $modificationStatement = new ModificationStatement();
        $modificationStatement->setStatement($statement);
        $modificationStatement->setModification($modification);
        $modificationStatement->setRefused(false);
        $manager->persist($modificationStatement);

        $manager->flush();

        $chosen = new ChosenModification();
        $chosen->setModificationStatement($modificationStatement);
        $chosen->setParagraph($paragraph1);
        $chosen->setStatement($statement);
        $chosen->setChosenBy($user);
        $manager->persist($chosen);
        $manager->flush();

        $paragraph1->addChosenModification($chosen);
        $manager->persist($paragraph1);
        $manager->flush();

        $thread = new Thread();
        $thread->setIdentifier('statement-'.$statement->getId().'-modification-'.$modification->getId());
        $manager->persist($thread);

        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setText('Das ist ein Testkommentar.');
        $comment->setThread($thread);
        $comment->setCreatedAt(new \DateTimeImmutable('-10days'));
        $manager->persist($comment);

        $comment1 = new Comment();
        $comment1->setAuthor($user2);
        $comment1->setText('Ein unterstützender Kommentar.');
        $comment1->setThread($thread);
        $comment1->setCreatedAt(new \DateTimeImmutable('-7days'));
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setAuthor($user3);
        $comment2->setText('Der dritte Kommentar, diesmal zur Diskriminierung der sogenannt Geisteskranken.');
        $comment2->setParent($comment1);
        $comment2->setThread($thread);
        $comment2->setCreatedAt(new \DateTimeImmutable('-5days'));
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setAuthor($user);
        $comment3->setText('Ein weiterer Testkommentar.');
        $comment3->setParent($comment1);
        $comment3->setThread($thread);
        $comment3->setCreatedAt(new \DateTimeImmutable('-1days'));
        $manager->persist($comment3);

        $comment4 = new Comment();
        $comment4->setAuthor($user2);
        $comment4->setText('Ein zusätzlicher Kommentar!');
        $comment4->setParent($comment2);
        $comment4->setThread($thread);
        $comment4->setCreatedAt(new \DateTimeImmutable('-5hours'));
        $manager->persist($comment4);

        $comment5 = new Comment();
        $comment5->setAuthor($user);
        $comment5->setText('Dummy data für die Plattform!');
        $comment5->setThread($thread);
        $comment5->setCreatedAt(new \DateTimeImmutable('-3days'));
        $manager->persist($comment5);

        $thread2 = new Thread();
        $thread2->setIdentifier('statement-'.$statement->getId().'-paragraph-'.$paragraph2->getId());
        $manager->persist($thread2);

        $comment6 = new Comment();
        $comment6->setAuthor($user);
        $comment6->setText('Hoch lebe die direkte Demokratie!');
        $comment6->setThread($thread2);
        $comment6->setCreatedAt(new \DateTimeImmutable('-3days'));
        $manager->persist($comment6);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dummy'];
    }
}
