<?php

namespace App\Command;

use App\Entity\Document;
use App\Repository\ConsultationRepository;
use App\Repository\DocumentRepository;
use App\Service\SparqlService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:consultations:documents', description: 'Pulls consultation documents from Fedlex')]
class PullConsultationDocumentsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ConsultationRepository $consultationRepository,
        private DocumentRepository $documentRepository,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp('Pulls documents of existing ongoing and planned consultations')
            ->addOption('legacy', false, InputOption::VALUE_NONE, 'Import also finished consultations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $legacy = $input->getOption('legacy');
        $status = ['ongoing', 'planned'];

        if ($legacy !== false) {
            $status = array_merge($status, ['pending_report', 'pending_statements_report', 'done']);
        }

        $consultations = $this->consultationRepository->findBy([
            'status' => $status,
        ]);

        $sparql = new SparqlService();

        foreach ($consultations as $i => $consultation) {
            $proposals = $sparql->getConsultationDocuments($consultation->getFedlexId(), true);
            $documents = $sparql->getConsultationDocuments($consultation->getFedlexId());

            foreach ($proposals as $j => $proposal) {
                if (!$this->documentRepository->findOneBy(['consultation' => $consultation, 'title' => $proposal->documentTitle->getValue()])) {
                    $document[$j] = new Document();
                    $document[$j]->setTitle($proposal->documentTitle->getValue());
                    $document[$j]->setConsultation($consultation);
                    $document[$j]->setType('proposal');
                    $document[$j]->setImported('fetched');
                    $document[$j]->setFedlexUri($proposal->document->getUri());
                    $document[$j]->setFilename(substr($proposal->document->getUri(), strrpos($proposal->document->getUri(), '/') + 1));

                    $this->entityManager->persist($document[$j]);

                    $output->writeln('Saved <info>"'.$proposal->documentTitle->getValue().'"</info> ('.$consultation->getFedlexId().')');
                } else {
                    $output->writeln('<comment>"'.$proposal->documentTitle->getValue().'"</comment> was already imported for <comment>'.$consultation->getFedlexId().'</comment>');
                }
            }

            foreach ($documents as $j => $importedDocument) {
                if (!$this->documentRepository->findOneBy(['consultation' => $consultation, 'title' => $importedDocument->documentTitle->getValue()])) {
                    $document[$j] = new Document();
                    $document[$j]->setTitle($importedDocument->documentTitle->getValue());
                    $document[$j]->setConsultation($consultation);
                    $document[$j]->setType('document');
                    $document[$j]->setImported('fetched');
                    $document[$j]->setFedlexUri($importedDocument->document->getUri());
                    $document[$j]->setFilename(substr($importedDocument->document->getUri(), strrpos($importedDocument->document->getUri(), '/') + 1));

                    $this->entityManager->persist($document[$j]);

                    $output->writeln('Saved <info>"'.$importedDocument->documentTitle->getValue().'"</info> ('.$consultation->getFedlexId().')');
                } else {
                    $output->writeln('<comment>"'.$importedDocument->documentTitle->getValue().'"</comment> was already imported for <comment>'.$consultation->getFedlexId().'</comment>');
                }
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
