<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Add a short description for your command',
)]
class WeatherLocationCommand extends Command
{

    private $locationRepository;
    private $weatherUtil;
    public function __construct(LocationRepository $locationRepository, WeatherUtil $weatherUtil)
    {
        parent::__construct();
        $this->locationRepository = $locationRepository;
        $this->weatherUtil = $weatherUtil;
    }

    protected function configure(): void
    {
        $this->setName('weather:location')
            ->setDescription('Pobiera prognozę pogody dla podanej lokalizacji.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID lokalizacji');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationId = $input->getArgument('id');
        $location = $this->locationRepository->find($locationId);
        if (!$location) {
            $io->error('Lokalizacja o podanym ID nie została znaleziona.');
            return Command::FAILURE;
        }
        $measurements = $this->weatherUtil->getWeatherForLocation($location);
        $io->title(sprintf('Location: %s', $location->getCity()));
        foreach ($measurements as $measurement) {
            $io->writeln(sprintf(
                "\t%s: %s°C",
                $measurement->getDate()->format('Y-m-d'),
                $measurement->getCelsius()
            ));
        }

        return Command::SUCCESS;
    }

}
