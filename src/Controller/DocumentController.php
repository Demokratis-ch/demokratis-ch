<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\LegalText;
use App\Entity\Paragraph;
use App\Form\LegalTextType;
use App\Service\LegalTextParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/d/')]
class DocumentController extends AbstractController
{
    private LegalTextParser $legalTextParser;

    public function __construct(LegalTextParser $legalTextParser)
    {
        $this->legalTextParser = $legalTextParser;
    }

    #[Route('/', name: 'app_document', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('document/index.html.twig', [
            'controller_name' => 'DocumentController',
        ]);
    }

    #[Route('{uuid}/import', name: 'app_consultation_import_legal', methods: ['GET', 'POST'])]
    public function importLegalText(Document $document, Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        if (!$document->getLegalText()) {
            $legalText = new LegalText();

            if (!$document->getContent()) {
                $dir = $params->get('file_directory').'/proposals/';
                $text = $this->legalTextParser->getParagraphs($dir.$document->getLocalfilename());
                $legalText->setText($text);
                $document->setImported('parsed');
            } else {
                $legalText->setText($document->getContent());
                $document->setImported('parsed');
                $text = $document->getContent();
            }
        } else {
            $legalText = $document->getLegalText();
            $text = $document->getLegalText()->getText();
        }

        $form = $this->createForm(LegalTextType::class, $legalText);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$document->getImported() != 'paragraphed') {
            $paragraphs = explode('!!!', $legalText->getText());

            // Remove first element if empty
            if (empty($paragraphs[0])) {
                unset($paragraphs[0]);
            }

            $legalText->setConsultation($document->getConsultation());
            $legalText->setImportedFrom($document);
            $legalText->setCreatedBy($this->getUser());
            $legalText->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($legalText);

            foreach ($paragraphs as $i => $sourceParagraph) {
                $paragraph[$i] = new Paragraph();
                $paragraph[$i]->setPosition($i * 100);
                $paragraph[$i]->setText($sourceParagraph);
                $paragraph[$i]->setLegalText($legalText);
                $paragraph[$i]->setCreatedAt(new \DateTimeImmutable());
                $paragraph[$i]->setUpdatedAt(new \DateTimeImmutable());
                $entityManager->persist($paragraph[$i]);
            }

            $document->setImported('paragraphed');
            $document->setType('proposal');

            if ($document->getLocalfilename()) {
                // Remove the file
                $fileSystem = new Filesystem();
                $fileSystem->remove($dir.$document->getLocalfilename());
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_show_statements', ['slug' => $legalText->getConsultation()->getSlug()]);
        }

        return $this->render('document/index.html.twig', [
            'form' => $form,
            'text' => $text,
            'document' => $document,
            'consultation' => $document->getConsultation(),
        ]);
    }
}
