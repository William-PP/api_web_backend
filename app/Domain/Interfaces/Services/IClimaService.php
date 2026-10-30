<?php

namespace App\Domain\Interfaces\Services;

interface IClimaService
{
    public function obtenerClima(string $ciudad): array;
}
