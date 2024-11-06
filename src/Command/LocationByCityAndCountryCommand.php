<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:LocationByCityAndCountryCommand',
    description: 'Add a short description for your command.',
)]
class LocationByCityAndCountryCommand extends Command
{
    private WeatherUtil $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        parent::__construct();
        $this->weatherUtil = $weatherUtil;
    }

    protected function configure(): void
    {
        $this->setName('weather:location-by-name')
            ->setDescription('Pobierz prognozę pogody na podstawie nazwy miasta i kodu kraju.')
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Kod kraju')
            ->addArgument('city', InputArgument::REQUIRED, 'Nazwa miejscowości');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument('countryCode');
        $city = $input->getArgument('city');
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);
        if (empty($measurements)) {
            $io->error('Nie udało się znaleźć lokalizacji lub pobrać prognozy pogody.');
            return Command::FAILURE;
        }
        $io->title(sprintf('Location: %s, %s', $city, $countryCode));
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
