<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:demote', description: 'Demotes a given user')]
class UserDemoteCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp('Removes all roles except ROLE_USER')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if ($user !== null) {
            if ($user->getRoles() === ['ROLE_USER']) {
                $output->writeln('<comment>'.$email.' is already a simple user</comment>');
            } else {
                $user->setRoles(['ROLE_USER']);

                $output->writeln('<info>Demoted '.$email.' to user</info>');

                $this->entityManager->flush();
            }
        } else {
            $output->writeln('<comment>There is no user with the email '.$email.'</comment>');
        }

        return Command::SUCCESS;
    }
}
