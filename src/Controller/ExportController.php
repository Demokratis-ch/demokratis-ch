<?php

namespace App\Controller;

use App\Entity\Statement;
use App\Form\ExportType;
use App\Repository\ChosenModificationRepository;
use App\Repository\DocumentRepository;
use App\Repository\FreeTextRepository;
use App\Repository\LegalTextRepository;
use App\Repository\ThreadRepository;
use App\Service\WordDiff;
use PhpOffice\PhpWord\Element\Comment;
use PhpOffice\PhpWord\Element\TrackChange;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ExportController extends AbstractController
{
    #[Route('/export/{uuid}', name: 'app_word_export', methods: ['GET', 'POST'])]
    public function export(Statement $statement, Request $request): Response
    {
        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('app_word_export_file', [
                'uuid' => $statement->getUuid(),
                'diffOutput' => $data['colored'] ? 1 : 0,
                'reasons' => $data['reasons'] ? 1 : 0,
                'freetext' => $data['freetext'] ? 1 : 0,
                'comments' => $data['comments'],
            ]);
        }

        return $this->render('export/export.html.twig', [
            'form' => $form,
            'statement' => $statement,
        ]);
    }

    #[Route('/export/file/{uuid}/{diffOutput}/{reasons}/{freetext}/{comments}', name: 'app_word_export_file', methods: ['GET'])]
    public function serveFile(
        Statement $statement,
        LegalTextRepository $legalTextRepository,
        DocumentRepository $documentRepository,
        ChosenModificationRepository $chosenModificationRepository,
        FreeTextRepository $freeTextRepository,
        ThreadRepository $threadRepository,
        bool $diffOutput = true,
        bool $reasons = false,
        bool $freetext = true,
        int $comments = 0,
    ): BinaryFileResponse {
        $phpWord = new PhpWord();

        // Adding an empty Section to the document
        $section = $phpWord->addSection();

        if ($statement->getIntro()) {
            $section->addText($statement->getIntro());
            $section->addTextBreak(3);
        }

        // Fetch legal texts
        $proposals = $documentRepository->findBy(['consultation' => $statement->getConsultation(), 'type' => 'proposal']);
        $legalTexts = $legalTextRepository->findBy(['consultation' => $statement->getConsultation()]);

        // Iterate over paragraphs and create diffs
        $paragraphs = [];
        foreach ($legalTexts as $legalText) {
            $section->addText($legalText->getTitle(), ['size' => 14, 'bold' => true]);
            $section->addTextBreak(2);

            foreach ($legalText->getParagraphs() as $i => $paragraph) {
                $paragraphs[$i]['paragraph'] = $paragraph;
                $chosen[$i] = $chosenModificationRepository->findOneBy(['statement' => $statement, 'paragraph' => $paragraph]);

                $paragraphs[$i]['freetext']['before'] = $freeTextRepository->findBy(['statement' => $statement, 'paragraph' => $paragraph, 'position' => 'before']);
                $paragraphs[$i]['freetext']['after'] = $freeTextRepository->findBy(['statement' => $statement, 'paragraph' => $paragraph, 'position' => 'after']);

                if ($freetext) {
                    foreach ($paragraphs[$i]['freetext']['before'] as $freetextContent) {
                        $section->addText($freetextContent->getText(), ['italic' => true]);
                        $section->addTextBreak(1);
                    }
                }

                $textRun = $section->addTextRun();

                if ($chosen[$i]) {
                    $paragraphs[$i]['chosen']['modification'] = $chosen[$i]->getModificationStatement()->getModification();

                    // Show diff, if $diffOutput is true
                    if ($diffOutput) {
                        $wd[$i] = new WordDiff();
                        $paragraphs[$i]['chosen']['diff'] = $wd[$i]->diff($paragraph->getText(), $chosen[$i]->getModificationStatement()->getModification()->getText());

                        foreach ($paragraphs[$i]['chosen']['diff'] as $diff) {
                            $text = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $diff[1]);
                            $text = str_replace("\r\n", '</w:t><w:br/><w:t xml:space="preserve">', $text);

                            if ($diff[0] == 0) {
                                $textRun->addText($text);
                            } elseif ($diff[0] == -1) {
                                $textRun->addText($text, ['color' => '991B1B', 'bgColor' => 'FEE2E2']);
                            } elseif ($diff[0] == 1) {
                                $textRun->addText($text, ['color' => '206D3D', 'bgColor' => 'DCFCE7']);
                            }
                        }
                    } else {
                        $text = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $paragraphs[$i]['chosen']['modification']->getText());
                        $text = str_replace("\r\n", '</w:t><w:br/><w:t xml:space="preserve">', $text);

                        $textRun->addText($text);
                    }
                    $textRun->addText('</w:t><w:br/><w:t xml:space="preserve">');

                    if ($comments !== 1) {
                        $thread[$i] = $threadRepository->findOneBy(['statement' => $statement, 'modification' => $paragraphs[$i]['chosen']['modification']]);

                        foreach ($thread[$i]->getComments() as $j => $comment) {
                            if ($comments === 2) {
                                $commentRun[$j] = $section->addTextRun('');

                                $wordComment[$j] = new Comment($comment->getAuthor(), new \DateTime());
                                $wordComment[$j]->addText($comment->getText());

                                $phpWord->addComment($wordComment[$j]);

                                $commentRun[$j]->setCommentStart($wordComment[$j]);
                                $commentRun[$j]->setCommentRangeEnd($wordComment[$j]);
                            } elseif ($comments === 3) {
                                $textRun->addText('</w:t><w:br/><w:t xml:space="preserve">');
                                $textRun->addText('</w:t><w:br/><w:t xml:space="preserve">');
                                $textRun->addText($comment->getText());
                            }
                        }
                    }

                    // Show reasons, if $reasons is true
                    if ($reasons) {
                        $section->addTextBreak(1);
                        $section->addText($paragraphs[$i]['chosen']['modification']->getJustification(), ['color' => 'gray', 'italic' => true]);
                    }
                }

                if ($freetext) {
                    foreach ($paragraphs[$i]['freetext']['after'] as $freetextContent) {
                        $section->addText($freetextContent->getText(), ['italic' => true]);
                        $section->addTextBreak(1);
                    }
                }

                // Add linebreak
                $section->addTextBreak(2);
            }
        }

        // New portrait section
        $section = $phpWord->addSection();
        $textRun = $section->addTextRun();

        $text = $textRun->addText('Hello World! Time to ');

        $text = $textRun->addText('wake ', ['bold' => true]);
        $text->setChangeInfo(TrackChange::INSERTED, 'Fred', time() - 1800);

        $text = $textRun->addText('up');
        $text->setTrackChange(new TrackChange(TrackChange::INSERTED, 'Fred'));

        $text = $textRun->addText('go to sleep');
        $text->setChangeInfo(TrackChange::DELETED, 'Barney', new \DateTime('@'.(time() - 3600)));

        // Preparing and serving the file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $slugger = new AsciiSlugger();
        $filename = substr($slugger->slug($statement->getName()), 0, 45);

        $filename = $filename.'-'.date('Y-m-d-H-i-s');
        $format = '.docx';
        $file = $filename.$format;
        $path = '../var/';

        $objWriter->save($path.$file);
        $response = new BinaryFileResponse($path.$file);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file
        );

        $response->deleteFileAfterSend(true);

        return $response;
    }
}
