<?php

namespace App\Command;

use App\Service\AdminSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ensure-admin',
    description: 'Crée ou synchronise le compte administrateur depuis les variables d\'environnement',
)]
final class EnsureAdminCommand extends Command
{
    public function __construct(private readonly AdminSeeder $seeder)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->seeder->seed();
        $io->success('Compte administrateur vérifié.');

        return Command::SUCCESS;
    }
}
