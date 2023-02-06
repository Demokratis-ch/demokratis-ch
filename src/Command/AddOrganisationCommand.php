<?php

namespace App\Command;

use App\Entity\Organisation;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(name: 'app:organisation:add', description: 'Creates a new organisation')]
class AddOrganisationCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrganisationRepository $organisationRepository,
        private SluggerInterface $slugger,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setAliases(['app:organisation:create'])
            ->setAliases(['app:orga:add'])
            ->setAliases(['app:orga:create'])
            ->setDescription('Creates a new organisation')
            ->setHelp('This command allows to create a new organisation')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the organisation.')
            ->addOption('person', 'p', InputOption::VALUE_OPTIONAL, 'Is this a personal organisation?', false
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        $organisation = $this->organisationRepository->findOneBy([
            'name' => $name,
        ]);

        if ($organisation === null) {
            $organisation = new Organisation();
            $organisation->setName($name);
            $organisation->setSlug(strtolower($this->slugger->slug($name)));
            $organisation->setPublic(true);
            $organisation->setIsPersonalOrganisation($input->getOption('person'));

            $this->entityManager->persist($organisation);
            $this->entityManager->flush();

            $output->writeln('<info>Added organisation "'.$organisation.'" (Id: '.$organisation->getId().')</info>');
        } else {
            $output->writeln('<comment>There is already an organisation with the name '.$organisation.'</comment>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
