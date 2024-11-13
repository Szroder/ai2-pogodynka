<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Attribute\MapQueryParameter;
use App\Service\WeatherUtil;
use App\Entity\Measurement;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        Request $request,
        WeatherUtil $weatherUtil,
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response {
        $country = $request->query->get('country');
        $city = $request->query->get('city');
        $format = $request->query->get('format', 'json');

        $measurements = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($twig) {
            if ($format === 'csv') {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
            return $this->render('weather_api/index.json.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);
        }

        if ($format === 'csv') {
            $csvData = "city,country,date,celsius,fahrenheit\n";

            foreach ($measurements as $measurement) {
                $csvData .= sprintf('%s,%s,%s,%.2f,%.2f' . "\n",
                    $city,
                    $country,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius(),
                    $measurement->getFahrenheit()
                );
            }

            $response = new Response($csvData);
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-Disposition', 'inline');

            return $response;
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn(Measurement $m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),
            ], $measurements),
        ]);
    }
}
