<?php

namespace App\Controller;

use App\Entity\Statement;
use App\Form\ExportType;
use App\Repository\ChosenModificationRepository;
use App\Repository\DocumentRepository;
use App\Repository\LegalTextRepository;
use DiffMatchPatch\DiffMatchPatch;
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
    #[Route('/export/{id}', name: 'app_word_export')]
    public function export(Statement $statement, Request $request): Response
    {
        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('app_word_export_file', [
                'id' => $statement->getId(),
                'diffOutput' => $data['colored'] ? 1 : 0,
                'reasons' => $data['reasons'] ? 1 : 0,
            ]);
        }

        return $this->render('export/export.html.twig', [
            'form' => $form,
            'statement' => $statement,
        ]);
    }

    #[Route('/export/file/{id}/{diffOutput}/{reasons}', name: 'app_word_export_file', methods: ['GET', 'POST'])]
    public function serveFile(
        Statement $statement,
        LegalTextRepository $legalTextRepository,
        DocumentRepository $documentRepository,
        ChosenModificationRepository $chosenModificationRepository,
        bool $diffOutput = true,
        bool $reasons = false,
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

                        $textRun = $section->addTextRun();

                        foreach ($paragraphs[$i]['chosen']['diff'] as $diff) {
                            if ($diff[0] == 0) {
                                $textRun->addText($diff[1]);
                            } elseif ($diff[0] == -1) {
                                $textRun->addText($diff[1], ['color' => '#991B1B', 'bgColor' => '#FEE2E2']);
                            } elseif ($diff[0] == 1) {
                                $textRun->addText($diff[1], ['color' => '#206D3D', 'bgColor' => '#DCFCE7']);
                            }
                        }
                    } else {
                        $section->addText($paragraphs[$i]['chosen']['modification']->getText());
                    }

                    // Show reasons, if $reasons is true
                    if ($reasons) {
                        $section->addText($paragraphs[$i]['chosen']['modification']->getJustification(), ['color' => 'gray', 'italic' => true]);
                    }
                }
                // Add linebreak
                $section->addTextBreak(2);
            }
        }

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
