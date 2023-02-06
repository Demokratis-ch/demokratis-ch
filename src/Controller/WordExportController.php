<?php

namespace App\Controller;

use App\Entity\Statement;
use App\Repository\ChosenModificationRepository;
use App\Repository\DocumentRepository;
use App\Repository\LegalTextRepository;
use DiffMatchPatch\DiffMatchPatch;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class WordExportController extends AbstractController
{
    #[Route('/word-export/{id}/{diffOutput}', name: 'app_word_export', methods: ['GET'])]
    public function test(
        Statement $statement,
        LegalTextRepository $legalTextRepository,
        DocumentRepository $documentRepository,
        ChosenModificationRepository $chosenModificationRepository,
        bool $diffOutput = true,
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

                if ($chosen[$i]) {
                    $paragraphs[$i]['chosen']['modification'] = $chosen[$i]->getModificationStatement()->getModification();

                    // Show diff, if $diffOutput is true
                    if ($diffOutput) {
                        $dmp[$i] = new DiffMatchPatch();
                        $paragraphs[$i]['chosen']['diff'] = $dmp[$i]->diff_main($paragraph->getText(), $chosen[$i]->getModificationStatement()->getModification()->getText(), false);

                        foreach ($paragraphs[$i]['chosen']['diff'] as $diff) {
                            if ($diff[0] == 0) {
                                $section->addText($diff[1]);
                            } elseif ($diff[0] == -1) {
                                $section->addText($diff[1], ['color' => 'darkRed']);
                            } elseif ($diff[0] == 1) {
                                $section->addText($diff[1], ['color' => 'darkGreen']);
                            }
                        }
                    } else {
                        $section->addText($paragraphs[$i]['chosen']['modification']->getText());
                    }
                }
                // Add linebreak
                $section->addTextBreak(2);
            }
        }

        // Preparing and serving the file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
        $slugger = new AsciiSlugger();
        $filename = substr($slugger->slug($statement->getName()), 0, 45);
        $objWriter->save('../var/'.$filename.'.odt', 'ODText', true);
        $response = new BinaryFileResponse('../var/'.$filename.'.odt');

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename.'.odt'
        );

        $response->deleteFileAfterSend(true);

        return $response;
    }
}
