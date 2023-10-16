<?php

namespace App\Command;

use App\Entity\Consultation;
use App\Entity\UnknownInstitution;
use App\Repository\ConsultationRepository;
use App\Repository\OrganisationRepository;
use App\Service\SparqlService;
use App\Service\TaggingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:consultations:pull', description: 'Pulls consultations from Fedlex')]
class PullConsultationsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ConsultationRepository $consultationRepository,
        private TaggingService $taggingService,
        private OrganisationRepository $organisationRepository,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp('Pulls consultations from Fedlex and stores new consultations in the db')
            ->addArgument('filter', InputArgument::OPTIONAL, 'The password of the user.', 'all')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filters = ['ongoing', 'planned', 'done', 'all'];

        if (!in_array($input->getArgument('filter'), $filters)) {
            $output->writeln('<error>"'.$input->getArgument('filter').'" is not a valid filter. Available options are: '.implode(', ', $filters).'</error>');

            return Command::INVALID;
        }

        $filter = match ($input->getArgument('filter')) {
            'ongoing' => 'Laufend',
            'planned' => 'Geplant',
            'done' => 'Abgeschlossen',
            default => 'all',
        };

        $sparql = new SparqlService();

        $consultations = $sparql->getConsultations($filter);

        foreach ($consultations as $i => $fetchedConsultation) {
            $existingConsultation = $this->consultationRepository->findOneBy(['fedlexId' => $fetchedConsultation->id]);
            $fetchedStatus = match ($fetchedConsultation->statLabel->getValue()) {
                'Geplant' => 'planned',
                'Laufend' => 'ongoing',
                'Abgeschlossen – abwarten Stellungnahmen und/oder des Ergebnisberichts' => 'pending_statements_report',
                'Abgeschlossen – abwarten Ergebnisbericht' => 'pending_report',
                'Abgeschlossen' => 'done',
                default => 'unknown',
            };

            $federal = $this->organisationRepository->findOneBy(['slug' => 'CH']);

            if (!$federal) {
                $output->writeln('Could not find the federal organization. Please create it.');

                return Command::FAILURE;
            }

            if (!$existingConsultation) {
                $consultation[$i] = new Consultation();
                $consultation[$i]->setOrganisation($federal);
                $consultation[$i]->setFedlexId($fetchedConsultation->id);
                $consultation[$i]->setDescription($fetchedConsultation->desc);
                $consultation[$i]->setTitle($fetchedConsultation->title);
                $consultation[$i]->setStatus($fetchedStatus);
                if (isset($fetchedConsultation->startDate)) {
                    $consultation[$i]->setStartDate(\DateTimeImmutable::createFromMutable($fetchedConsultation->startDate->getValue()));
                }
                if (isset($fetchedConsultation->endDate)) {
                    $consultation[$i]->setEndDate(\DateTimeImmutable::createFromMutable($fetchedConsultation?->endDate->getValue()));
                }
                $consultation[$i]->setOffice($fetchedConsultation->officeLabel);
                $consultation[$i]->setInstitution($fetchedConsultation->institutionLabel);

                // Assign tags based on institution
                $tagging = $this->taggingService;
                $tags = $tagging->extractTags($consultation[$i]->getInstitution());

                if ($tags === 'unmatched') {
                    $unknown[$i] = new UnknownInstitution();
                    $unknown[$i]->setConsultation($consultation[$i]);
                    $unknown[$i]->setInstitution($fetchedConsultation->institutionLabel);
                    $unknown[$i]->setCreatedAt(new \DateTimeImmutable());
                    $this->entityManager->persist($unknown[$i]);
                } else {
                    foreach ($tags as $tag) {
                        $consultation[$i]->addTag($tagging->getTagEntity($tag));
                    }
                }

                $this->entityManager->persist($consultation[$i]);

                $output->writeln('Added <info>'.$fetchedConsultation->id.'</info> to the database');
            } else {
                $consultation[$i] = $existingConsultation;
                $consultation[$i]->setStatus($fetchedStatus);
                $consultation[$i]->setDescription($fetchedConsultation->desc);
                $consultation[$i]->setTitle($fetchedConsultation->title);
                if (isset($fetchedConsultation->startDate)) {
                    $consultation[$i]->setStartDate(\DateTimeImmutable::createFromMutable($fetchedConsultation->startDate->getValue()));
                }
                if (isset($fetchedConsultation->endDate)) {
                    $consultation[$i]->setEndDate(\DateTimeImmutable::createFromMutable($fetchedConsultation?->endDate->getValue()));
                }
                $consultation[$i]->setOffice($fetchedConsultation->officeLabel);
                $consultation[$i]->setInstitution($fetchedConsultation->institutionLabel);

                $this->entityManager->persist($consultation[$i]);

                $output->writeln('Updated <info>'.$fetchedConsultation->id.'</info>, Status: "'.$fetchedStatus.'"');
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
