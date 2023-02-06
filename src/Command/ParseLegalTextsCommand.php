<?php

namespace App\Command;

use App\Entity\LegalText;
use App\Repository\DocumentRepository;
use App\Service\LegalTextParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:consultations:pull-texts', description: 'Pull and parse the legal texts of documents')]
class ParseLegalTextsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DocumentRepository $documentRepository,
        private LegalTextParser $legalTextParser,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $documents = $this->documentRepository->findBy([
            'type' => 'proposal',
            'imported' => ['fetched'],
        ]);

        foreach ($documents as $i => $document) {
            $text = $this->legalTextParser->getParagraphs($document->getFedlexFilepath());
            $text = iconv(mb_detect_encoding($text, mb_detect_order(), true), 'utf-8//IGNORE', $text);

            $legalText = new LegalText();
            $legalText->setText($text);
            $this->entityManager->persist($legalText);

            $document->setLegalText($legalText);
            $document->setImported('parsed');
            $this->entityManager->persist($document);

            $output->writeln('Fetched and parsed <info>"'.$document->getTitle().'"</info> ('.$document->getFedlexUri().')');
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
