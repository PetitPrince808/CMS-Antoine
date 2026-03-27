<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Commande pour créer rapidement un admin en dev — ne pas exposer en prod
#[AsCommand(name: 'app:create-admin', description: 'Crée un utilisateur administrateur')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('admin@cms-disii.local');
        $user->setNom('Administrateur');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Admin créé : admin@cms-disii.local / admin1234');

        return Command::SUCCESS;
    }
}
