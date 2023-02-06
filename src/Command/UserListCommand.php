<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:list', description: 'Lists all admin users')]
class UserListCommand extends Command
{
    public function __construct(
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
            ->setHelp('This command lists all admin users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $admins = $this->userRepository->findByRole('ROLE_ADMIN');
        $output->writeln('<info>Admins:</info>');

        if (!$admins) {
            $output->writeln('There are no admins');
        } else {
            foreach ($admins as &$admin) {
                $output->writeln($admin->getEmail());
            }
        }

        $super_admins = $this->userRepository->findByRole('ROLE_SUPER_ADMIN');

        $output->writeln('<info>Super admins:</info>');

        if (!$super_admins) {
            $output->writeln('There are no super admins');
        } else {
            foreach ($super_admins as &$super_admin) {
                $output->writeln($super_admin->getEmail());
            }
        }

        return Command::SUCCESS;
    }
}
