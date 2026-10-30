<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\Infrastructure\IWeatherClient;
use App\Domain\Interfaces\Services\IClimaService;

class ClimaService implements IClimaService
{
    public function __construct(private IWeatherClient $weather)
    {
    }

    public function obtenerClima(string $ciudad): array
    {
        return $this->weather->getWeather($ciudad);
    }
}
