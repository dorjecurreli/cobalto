<?php

namespace App\Command;

use App\Service\ArtworkImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-artworks-from-storage')]
class ImportArtworksFromStorageCommand extends Command
{
    private ArtworkImporter $importer;

    public function __construct(ArtworkImporter $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importer->importArtworks();
        $output->writeln('Artworks imported successfully from storage!');

        return Command::SUCCESS;
    }
}

