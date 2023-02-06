<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:database:prime', description: 'Primes the production database')]
class SetupCommand extends Command
{
    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->manager = $manager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows to prime the production database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user[0] = new User();
        $user[0]->setUuid(Uuid::v4());
        $user[0]->setEmail('fabian@demokratis.ch');
        $user[0]->setPassword($this->passwordHasher->hashPassword($user[0], 'SoEinDemokrat'));
        $user[0]->setRoles(['ROLE_SUPER_ADMIN']);
        $user[0]->setIsVerified(true);
        $this->manager->persist($user[0]);

        $this->manager->flush();

        $output->writeln('<info>Primed the database</info>');

        return Command::SUCCESS;
    }
}
