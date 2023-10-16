<?php

namespace App\Command;

use App\Entity\Consultation;
use App\Entity\Document;
use App\Entity\Organisation;
use App\Enums\Cantons;
use App\Repository\ConsultationRepository;
use App\Repository\DocumentRepository;
use App\Repository\OrganisationRepository;
use App\Service\FetchCantonalConsultation;
use App\Service\TaggingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:cantons:pull', description: 'Pulls cantonal consultations')]
class PullCantonalConsultationsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FetchCantonalConsultation $cantonal,
        private OrganisationRepository $organisationRepository,
        private ConsultationRepository $consultationRepository,
        private TaggingService $taggingService,
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
            ->setHelp('Pulls consultations from Cantons')
            ->addArgument('canton', InputArgument::OPTIONAL, 'From which canton should we pull the consultations.', 'all')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('canton') === 'all') {
            foreach (Cantons::cases() as $key => $canton) {
                $organisation = $this->organisationRepository->findOneBy(['slug' => $canton->name]);

                if ($organisation) {
                    $message = $this->fetchConsultations($organisation);

                    $output->writeln($message);
                } else {
                    $output->writeln($canton->name.' is not a valid canton.');
                }
            }

            return Command::SUCCESS;
        } else {
            $canton = $this->organisationRepository->findOneBy(['slug' => $input->getArgument('canton')]);

            if ($canton) {
                $message = $this->fetchConsultations($input->getArgument('canton'));

                $output->writeln($message);

                return Command::SUCCESS;
            } else {
                $output->writeln($input->getArgument('canton').' is not a valid canton.');
            }
        }

        return Command::FAILURE;
    }

    protected function fetchConsultations(Organisation $organisation)
    {
        $content = $this->cantonal->getConsultations($organisation->getSlug());

        $output[] = 'Import for Canton <info>'.$organisation->getSlug().'</info>';

        foreach ($content['data'] as $key => $consultation) {
            $identifier = $organisation->getSlug().'-'.$consultation['affair_politmonitor_id'];
            $existingConsultation = $this->consultationRepository->findOneBy(['foreignId' => $identifier]);

            if (!$existingConsultation) {
                $consultation[$identifier] = new Consultation();
                $consultation[$identifier]->setOrganisation($organisation);
                $consultation[$identifier]->setForeignId($identifier);
                $consultation[$identifier]->setTitle(trim($consultation['affair_title_de']));

                $startDate = date_create_immutable_from_format('Y/m/d H:i:s.000', $consultation['affair_date_start']) ?? null;
                if ($startDate) {
                    $consultation[$identifier]->setStartDate($startDate);
                }

                // TODO Detect institutions
                $consultation[$identifier]->setOffice($consultation['affair_author_name']);

                // Add Tags
                if (isset($consultation['affair_topics'])) {
                    foreach ($consultation['affair_topics'] as $topic) {
                        $tag = match ($topic['topic_code']) {
                            '263' => $this->taggingService->getTagEntity('energie'),
                            '2' => $this->taggingService->getTagEntity('bildung'),
                            '10' => $this->taggingService->getTagEntity('bildung'),
                            '5' => $this->taggingService->getTagEntity('forschung'),
                            '250' => $this->taggingService->getTagEntity('informatik'),
                            '396' => $this->taggingService->getTagEntity('landwirtschaft'),
                            '646' => $this->taggingService->getTagEntity('wirtschaft'),
                            '251' => $this->taggingService->getTagEntity('wirtschaft'),
                            '252' => $this->taggingService->getTagEntity('wirtschaft'),
                            '366' => $this->taggingService->getTagEntity('wirtschaft'),
                            '264' => $this->taggingService->getTagEntity('finanzen'),
                            '16' => $this->taggingService->getTagEntity('gesundheit'),
                            '20' => $this->taggingService->getTagEntity('gesundheit'),
                            '23' => $this->taggingService->getTagEntity('gesundheit'),
                            '30' => $this->taggingService->getTagEntity('gesundheit'),
                            '7' => $this->taggingService->getTagEntity('gesundheit'),
                            '44' => $this->taggingService->getTagEntity('gesundheit'),
                            '148' => $this->taggingService->getTagEntity('gesundheit'),
                            '266' => $this->taggingService->getTagEntity('kultur'),
                            '269' => $this->taggingService->getTagEntity('sicherheit'),
                            '416' => $this->taggingService->getTagEntity('recht'),
                            '356' => $this->taggingService->getTagEntity('recht'),
                            '272' => $this->taggingService->getTagEntity('verkehr'),
                            '271' => $this->taggingService->getTagEntity('umwelt'),
                            '249' => $this->taggingService->getTagEntity('kommunikation'),
                            '243' => $this->taggingService->getTagEntity('migration'),
                            '268' => $this->taggingService->getTagEntity('raumplanung'),
                            '265' => $this->taggingService->getTagEntity('diplomatie'),
                            default => null,
                        };

                        if (!$tag) {
                            // TODO: Log missing tags
                        } else {
                            $consultation[$identifier]->addTag($tag);
                        }
                    }
                }

                // TODO Set correct deadlines and status
                if (isset($consultation['affair_events'])) {
                    foreach ($consultation['affair_events'] as $event) {
                        if (isset($event['event_text_de'])) {
                            if ($event['event_text_de'] === 'Frist') {
                                $endDate = date_create_immutable_from_format('Y/m/d H:i:s.000', $event['event_date']) ?? null;

                                if ($endDate) {
                                    $consultation[$identifier]->setEndDate($endDate);

                                    if ($endDate < new \DateTimeImmutable()) {
                                        $consultation[$identifier]->setStatus('done');
                                    } elseif ($endDate > new \DateTimeImmutable()) {
                                        $consultation[$identifier]->setStatus('ongoing');
                                    }
                                }
                            } elseif ($event['event_text_de'] === 'abgeschlossen') {
                                $consultation[$identifier]->setStatus('done');
                            }
                        }
                    }
                }

                if ($consultation['affair_closed']) {
                    $consultation[$identifier]->setStatus('done');
                }

                $this->entityManager->persist($consultation[$identifier]);

                $output[] = '#'.$key.' <info>'.$consultation['affair_title_de'].'</info>';

                // TODO Add documents
                if (isset($consultation['affair_documents'])) {
                    foreach ($consultation['affair_documents'] as $i => $fetchedDocument) {
                        if (!$this->documentRepository->findOneBy(['consultation' => $consultation[$identifier], 'originalUri' => $fetchedDocument['doc_link']])) {
                            $document[$i] = new Document();
                            $document[$i]->setTitle(substr($fetchedDocument['doc_name'], 0, 255));
                            $document[$i]->setConsultation($consultation[$identifier]);
                            $document[$i]->setType('document');
                            $document[$i]->setImported('fetched');
                            $document[$i]->setOriginalUri($fetchedDocument['doc_link']);

                            if (isset($fetchedDocument['doc_content'])) {
                                $document[$i]->setContent($fetchedDocument['doc_content']);
                            }

                            $this->entityManager->persist($document[$i]);

                            $output[] = 'Added <info>'.$fetchedDocument['doc_name'].'</info>';
                        } else {
                            $output[] = '<comment>"'.$fetchedDocument['doc_name'].'"</comment> was already imported for <comment>'.$consultation[$identifier].'</comment>';
                        }
                    }
                }
            } else {
                // TODO: Update existing consultation

                $output[] = 'Existing "'.$consultation['affair_title_de'].'"';
            }
        }

        $this->entityManager->flush();

        return $output;
    }
}
