<?php

namespace App\Domain\Interfaces\Infrastructure;

interface IWeatherClient
{
    public function getWeather(string $city): array;
}
