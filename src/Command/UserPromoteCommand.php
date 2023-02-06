<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:promote', description: 'Promotes a given user')]
class UserPromoteCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp('This command allows to promote a user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addOption('super', false, InputOption::VALUE_NONE, 'Makes the user a super admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);
        $optionValue = $input->getOption('super');
        $role = null;

        if (!$user) {
            $output->writeln('<comment>User '.$email.' could not be found</comment>');
        } else {
            $roles = $user->getRoles();

            if ($optionValue !== false) {
                if (in_array('ROLE_SUPER_ADMIN', $roles, true)) {
                    $output->writeln('<comment>'.$email.' is already super admin</comment>');
                } else {
                    $roles[] = 'ROLE_SUPER_ADMIN';
                    $role = 'super admin';
                }
            } else {
                if (in_array('ROLE_ADMIN', $roles, true)) {
                    $output->writeln('<comment>'.$email.' is already admin</comment>');
                } else {
                    $roles[] = 'ROLE_ADMIN';
                    $role = 'admin';
                }
            }

            $user->setRoles($roles);
            $this->entityManager->flush();

            if ($role !== null) {
                $output->writeln('<info>Promoted '.$email.' to '.$role.'</info>');
            }
        }

        return Command::SUCCESS;
    }
}
