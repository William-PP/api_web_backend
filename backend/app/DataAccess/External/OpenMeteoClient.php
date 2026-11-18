<?php

namespace App\DataAccess\External;

use App\Domain\Exceptions\BusinessException;
use App\Domain\Interfaces\Infrastructure\IWeatherClient;
use Illuminate\Support\Facades\Http;

class OpenMeteoClient implements IWeatherClient
{
    public function getWeather(string $city): array
    {
        $geocoding = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $city,
            'count' => 1,
            'language' => 'es',
            'format' => 'json',
        ]);

        $location = $geocoding->json('results.0');

        if (! $location) {
            throw new BusinessException("No se encontró la ciudad '{$city}'.");
        }

        $weather = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
            'timezone' => 'auto',
        ]);

        if ($weather->failed()) {
            throw new BusinessException('No se pudo obtener el clima en este momento.');
        }

        $current = $weather->json('current', []);

        return [
            'ciudad' => $location['name'],
            'pais' => $location['country'] ?? null,
            'latitud' => $location['latitude'],
            'longitud' => $location['longitude'],
            'temperatura' => $current['temperature_2m'] ?? null,
            'humedad' => $current['relative_humidity_2m'] ?? null,
            'viento' => $current['wind_speed_10m'] ?? null,
            'codigo_clima' => $current['weather_code'] ?? null,
            'actualizado_en' => $current['time'] ?? null,
        ];
    }
}
