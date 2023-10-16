<?php

namespace App\Command;

use App\Repository\ConsultationRepository;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:organisation:default', description: 'Adds the default organisation')]
class AddDefaultOrganisationCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrganisationRepository $organisationRepository,
        private ConsultationRepository $consultationRepository,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setAliases(['app:organisation:default'])
            ->setDescription('Adds the default organisation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $confederation = $this->organisationRepository->findOneBy(['slug' => 'CH']);

        if (!$confederation) {
            $output->writeln('<comment>Could not find the federal organisation. Please add it with the Slug "CH" before proceeding.</comment>');

            return Command::FAILURE;
        }

        $consultations = $this->consultationRepository->findBy(['organisation' => null]);

        foreach ($consultations as $consultation) {
            if (!$consultation->getOrganisation()) {
                $consultation->setOrganisation($confederation);
                $this->entityManager->persist($consultation);

                $output->writeln('<comment>Consultation '.$consultation->getId().' has no organisation. Adding the federal organisation.</comment>');
            }
        }

        $this->entityManager->flush();

        $output->writeln('<info>Added the confederation to all consultations without any organisation</info>');

        return Command::SUCCESS;
    }
}
