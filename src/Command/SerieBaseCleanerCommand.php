<?php

namespace App\Command;

use App\Entity\Serie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'serie:base-cleaner',
    description: 'Nettoie notre bdd des séries périmées',
)]
class SerieBaseCleanerCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nb_annees', InputArgument::REQUIRED, 'Nombre d\'années au-dela duquel on supprime les séries')
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'Simulation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nbAnnees = $input->getArgument('nb_annees');

        if ($nbAnnees) {
            $io->note(sprintf('Age limite des séries: %d ans', $nbAnnees));
        }

        $repository = $this->entityManager->getRepository(Serie::class);

        $series = $repository->findSeriesOldest($nbAnnees);
        $nbSeriesDeleted = count($series);

        try {
            foreach ($series as $serie) {
                $this->entityManager->remove($serie);
            }

            if (!$input->getOption('dry')) {
                $this->entityManager->flush();
                $io->success(sprintf('%d series supprimées', $nbSeriesDeleted));
            } else {
                $io->info(sprintf('%d series peuvent être supprimées', $nbSeriesDeleted));
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;

        /**
        $io->text('Bonjour');

        $reponse = $io->ask('Comment ça va ?');

        $reponse2= $io->confirm('Etes-vous sûr ... ?');

        $reponse3 = $io->choice('Quel parfum pour la glace ?', ['Vanille', 'Fraise', 'Citron']);

        $io->error('Y\'en a a plus');

        $io->writeln('Si: il y en a encore !!!');
**/

    }
}
