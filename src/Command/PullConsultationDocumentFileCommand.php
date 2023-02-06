<?php

namespace App\Command;

use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[AsCommand(name: 'app:consultations:files', description: 'Pulls consultation files from Fedlex')]
class PullConsultationDocumentFileCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DocumentRepository $documentRepository,
        ParameterBagInterface $params,
    ) {
        parent::__construct();
        $this->params = $params;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp('Pulls files of existing ongoing and planned consultations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $proposals = $this->documentRepository->findDocumentsWithoutFiles();

        foreach ($proposals as $i => $proposal) {
            $filesystem = new Filesystem();

            $dir = $this->params->get('file_directory').'/proposals/';
            $filename = $proposal->getConsultation()->getId().'-'.$proposal->getFileName().'.pdf';

            $proposal->setLocalfilename($filename);
            $proposal->setFilepath($dir.$filename);

            try {
                $filesystem->copy($proposal->getFedlexFilepath(), $dir.$filename);

                $this->entityManager->persist($proposal);
            } catch (FileException $e) {
                $output->writeln('<error>Could not import "'.$proposal->getFedlexFilepath().'"</error>');
            }

            $output->writeln('Imported <info>"'.$proposal->getFedlexFilepath().'"</info> ');
        }

        if (count($proposals) === 0) {
            $output->writeln('<info>No documents without files found</info>');
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
