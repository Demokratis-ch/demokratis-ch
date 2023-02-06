<?php

namespace App\Command;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:tag:add', description: 'Creates a new tag')]
class AddTagCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagRepository $tagRepository,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setAliases(['app:tag:create'])
            ->setDescription('Creates a new tag')
            ->setHelp('This command allows to create a new tag')
            ->addArgument('name', InputArgument::REQUIRED, 'The tag name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $tag = $this->tagRepository->findOneBy([
            'name' => $name,
        ]);

        if ($tag === null) {
            $tag = new Tag();
            $tag->setName($name);
            $tag->setApproved(true);
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $output->writeln('<info>Added Tag "'.$name.'"</info>');
        } else {
            $output->writeln('<comment>There is already a Tag "'.$name.'"</comment>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
