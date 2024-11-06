<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\WeatherUtil;

class WeatherController extends AbstractController
{
    #[Route('/weather/{country}/{city}', name: 'app_weather')]
    public function city(
        #[MapEntity(mapping: ['country' => 'country', 'city' => 'city'])] Location $location,
        WeatherUtil $util
    ): Response {
        $measurements = $util->getWeatherForLocation($location);
        $location = $locationRepository->findByOne($city, $country);

        if (!$measurements or !$location) {
            throw new \Exception('Nie udało się pobrać danych o pogodzie dla podanej lokalizacji.');
        }

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}