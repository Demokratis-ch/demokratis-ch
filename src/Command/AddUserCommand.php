<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserOrganisation;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:user:add', description: 'Creates a new user')]
class AddUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private OrganisationRepository $organisationRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setAliases(['app:user:create'])
            ->setDescription('Creates a new user')
            ->setHelp('This command allows to create a new user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
            ->addArgument('organisation', InputArgument::REQUIRED, 'The organisation the user belongs to.')
            ->addOption('admin', false, InputOption::VALUE_NONE, 'Makes the user a super admin')
            ->addOption('superadmin', false, InputOption::VALUE_NONE, 'Makes the user a super admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $organisation_id = $input->getArgument('organisation');

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        $organisation = $this->organisationRepository->findOneBy(['id' => $organisation_id]);

        $admin = $input->getOption('admin');
        $superadmin = $input->getOption('superadmin');

        $role = null;

        if ($user === null) {
            if ($organisation === null) {
                $output->writeln('<comment>There is no organisation with the id '.$organisation.'</comment>');

                return Command::FAILURE;
            }

            $user = new User();
            $user->setIsVerified(true);
            $user->setEmail($email);
            $user->setActiveOrganisation($organisation);

            $userOrganisation = new UserOrganisation();
            $userOrganisation->setOrganisation($organisation);
            $userOrganisation->setUser($user);
            $userOrganisation->setIsAdmin(false);
            $this->entityManager->persist($userOrganisation);

            $user->setPassword($this->passwordHasher->hashPassword($user, $password));

            if ($admin !== false) {
                $user->setRoles(['ROLE_ADMIN']);
                $role = 'admin';
            } elseif ($superadmin !== false) {
                $user->setRoles(['ROLE_SUPER_ADMIN']);
                $role = 'super admin';
            } else {
                $user->setRoles(['ROLE_USER']);
                $role = 'user';
            }

            $output->writeln('<info>Added '.$email.' as '.$role.'</info>');

            $this->entityManager->persist($user);

            $this->entityManager->flush();
        } else {
            $output->writeln('<comment>There is already an user with the email '.$email.'</comment>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
