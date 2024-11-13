<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Measurement;

class MeasurementTest extends TestCase
{
    /**
     * @dataProvider dataGetFahrenheit
     */
    public function testGetFahrenheit($celsius, $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setCelsius($celsius);
        $this->assertEqualsWithDelta($expectedFahrenheit, $measurement->getFahrenheit(), 0.01, sprintf('%s Celsius should be %s Fahrenheit', $celsius, $expectedFahrenheit));
    }

    public function dataGetFahrenheit(): array
    {
        return [
            ['0', 32.0],
            ['-100', -148.0],
            ['100', 212.0],
            ['0.5', 32.9],
            ['-40', -40.0],
            ['37.5', 99.5],
            ['22.8', 73.04],
            ['-12.7', 9.14],
            ['15.2', 59.36],
            ['-25.3', -13.54],
        ];
    }
}
