<?php

namespace App\DataFixtures;

use App\Entity\ChosenModification;
use App\Entity\Comment;
use App\Entity\Consultation;
use App\Entity\Discussion;
use App\Entity\Document;
use App\Entity\FreeText;
use App\Entity\LegalText;
use App\Entity\Media;
use App\Entity\Modification;
use App\Entity\ModificationStatement;
use App\Entity\Organisation;
use App\Entity\Paragraph;
use App\Entity\ParagraphStatement;
use App\Entity\Statement;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ConsultationStimmUndWahlrecht16JaehrigeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const CONSULTATION = 'consultation';
    public const STATEMENT = 'statement';

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
        $consultation->setTitle('Pa.Iv. Aktives Stimm- und Wahlrecht für 16-Jährige');
        $consultation->setDescription('Die Kommission schlägt vor, die Bundesverfassung so zu ändern, dass das aktive Stimm- und Wahlrechtsalter von 18 auf 16 Jahre gesenkt wird. Das Mindestalter für die Wählbarkeit in politische Ämter und an das Bundesgericht soll bei 18 Altersjahren belassen werden.');
        $consultation->setStatus('ongoing');
        $consultation->setFedlexId('proj/2022/59/cons_1');
        $consultation->setStartDate(new \DateTimeImmutable('2022-09-12 00:00:00.0'));
        $consultation->setEndDate(new \DateTimeImmutable('2022-12-16 00:00:00.0'));
        $consultation->setOffice('Parlamentarische Kommissionen');

        $manager->persist($consultation);
        $this->addReference(self::CONSULTATION, $consultation);

        $document = new Document();
        $document->setConsultation($consultation);
        $document->setTitle('Vernehmlassungsvorlage');
        $document->setType('proposal');
        $document->setFedlexUri('https://fedlex.data.admin.ch/eli/dl/proj/2022/59/cons_1/doc_1');
        $document->setFilename('doc_1');
        $document->setImported('paragraphed');

        $manager->persist($document);

        $document2 = new Document();
        $document2->setConsultation($consultation);
        $document2->setTitle('Anhang');
        $document2->setType('document');
        $document2->setFedlexUri('https://fedlex.data.admin.ch/eli/dl/proj/2022/59/cons_1/doc_1');
        $document2->setFilename('doc_2');

        $manager->persist($document2);

        $discussionThread = new Thread();

        $manager->persist($discussionThread);

        $discussionComment = new Comment();
        $discussionComment->setAuthor($user);
        $discussionComment->setText('Das ist eine Testdiskussion.');
        $discussionComment->setThread($discussionThread);
        $discussionComment->setCreatedAt(new \DateTimeImmutable('-10days'));
        $manager->persist($discussionComment);

        $discussion = new Discussion();
        $discussion->setConsultation($consultation);
        $discussion->setThread($discussionThread);
        $discussion->setTopic('Testdiskussion');
        $discussion->setCreatedAt(new \DateTimeImmutable());
        $discussion->setCreatedBy($user);

        $manager->persist($discussion);

        $media = new Media();
        $media->setConsultation($consultation);
        $media->setCreatedBy($user);
        $media->setTitle('Artikel in der Zeitung');
        $media->setCreatedAt(new \DateTimeImmutable());
        $media->setUrl('https://www.demokratis.ch');

        $manager->persist($media);

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

        $statement->setOrganisation($organisation);
        $statement->setConsultation($consultation);
        $statement->setName('Meine Meinung');
        $manager->persist($statement);
        $this->addReference(self::STATEMENT, $statement);

        $statement_foreign = new Statement();
        $statement_foreign->setPublic(true);

        $statement_foreign->setOrganisation($organisation);
        $statement_foreign->setConsultation($consultation);
        $statement_foreign->setName('Fremde Meinung');
        $manager->persist($statement_foreign);

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
        $modification->setJustification('Ich finde das so besser, da es inklusiver und schwammiger formuliert ist.');
        $manager->persist($modification);

        $thread = new Thread();
        $manager->persist($thread);

        $modificationStatement1 = new ModificationStatement();
        $modificationStatement1->setStatement($statement);
        $modificationStatement1->setModification($modification);
        $modificationStatement1->setRefused(false);
        $modificationStatement1->setDecisionReason('');
        $modificationStatement1->setThread($thread);
        $manager->persist($modificationStatement1);

        // modification is also part of another statement
        $modificationStatement1Foreign = new ModificationStatement();
        $modificationStatement1Foreign->setStatement($statement_foreign);
        $modificationStatement1Foreign->setModification($modification);
        $modificationStatement1Foreign->setRefused(false);
        $manager->persist($modificationStatement1Foreign);

        for ($x = 0; $x <= 6; ++$x) {
            $genericModification = new Modification();
            $genericModification->setCreatedBy($user);
            $genericModification->setText(
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
            $genericModification->setParagraph($paragraph1);
            $genericModification->setJustification('Ich finde das so besser, da es inklusiver und schwammiger formuliert ist.');
            $genericModification->setCreatedAt(new \DateTimeImmutable('-'.$x.'days'));
            $manager->persist($genericModification);

            $genericModificationStatement = new ModificationStatement();
            $genericModificationStatement->setStatement($statement);
            $genericModificationStatement->setModification($genericModification);
            $genericModificationStatement->setRefused(false);
            $genericModificationStatement->setDecisionReason('');
            $manager->persist($genericModificationStatement);
        }

        // modification shown in "inspirations"
        $foreignModification = new Modification();
        $foreignModification->setCreatedBy($user3);
        $foreignModification->setText(
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
        $foreignModification->setParagraph($paragraph1);
        $foreignModification->setJustification('Ich finde das so besser, da es inklusiver und schwammiger formuliert ist.');
        $manager->persist($foreignModification);

        $foreignModificationStatement = new ModificationStatement();
        $foreignModificationStatement->setStatement($statement_foreign);
        $foreignModificationStatement->setModification($foreignModification);
        $foreignModificationStatement->setRefused(false);
        $manager->persist($foreignModificationStatement);

        $foreignChosen = new ChosenModification();
        $foreignChosen->setModificationStatement($foreignModificationStatement);
        $foreignChosen->setParagraph($paragraph1);
        $foreignChosen->setStatement($statement_foreign);
        $foreignChosen->setChosenBy($user3);
        $manager->persist($foreignChosen);

        $manager->flush();

        $chosen = new ChosenModification();
        $chosen->setModificationStatement($modificationStatement1);
        $chosen->setParagraph($paragraph1);
        $chosen->setStatement($statement);
        $chosen->setChosenBy($user);
        $manager->persist($chosen);
        $manager->flush();

        $manager->persist($paragraph1);
        $manager->flush();

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
        $paragraph2Statement = ParagraphStatement::create($statement, $paragraph2, $thread2);
        $manager->persist($paragraph2Statement);
        $manager->flush();

        $comment6 = new Comment();
        $comment6->setAuthor($user);
        $comment6->setText('Hoch lebe die direkte Demokratie!');
        $comment6->setThread($thread2);
        $comment6->setCreatedAt(new \DateTimeImmutable('-3days'));
        $manager->persist($comment6);

        $paragraph1Thread = new Thread();
        $paragraph1Statement = ParagraphStatement::create($statement, $paragraph1, $paragraph1Thread);
        $manager->persist($paragraph1Statement);
        $manager->flush();

        $comment11 = new Comment();
        $comment11->setAuthor($user);
        $comment11->setText('Kommentar zum Original');
        $comment11->setThread($paragraph1Thread);
        $comment11->setCreatedAt(new \DateTimeImmutable('-12days'));
        $manager->persist($comment11);

        $manager->flush();

        $freeText1 = new FreeText();
        $freeText1->setStatement($statement);
        $freeText1->setParagraph($paragraph1);
        $freeText1->setPosition('before');
        $freeText1->setText('Wir als Verein "Freunde des demokratischen Hedonismus" finden es wichtig, neben den Pflichten auch auf die Freuden demokratischer Teilhabe hinzuweisen. Weiter sehen wir es als essentiell an, diese Freuden möglichst früh erlebbar zu machen:');
        $manager->persist($freeText1);

        $freeText2 = new FreeText();
        $freeText2->setStatement($statement);
        $freeText2->setParagraph($paragraph2);
        $freeText2->setPosition('after');
        $freeText2->setText('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec placerat nibh ex, nec suscipit ligula mollis at. Curabitur tincidunt tincidunt mi, vel tincidunt magna elementum ut.');
        $manager->persist($freeText2);
        $manager->flush();
    }
}
