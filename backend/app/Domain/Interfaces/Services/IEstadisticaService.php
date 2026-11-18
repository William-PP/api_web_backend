<?php

namespace App\Domain\Interfaces\Services;

interface IEstadisticaService
{
    /** @return array<int, array{modalidad: string, total: int}> */
    public function deportistasPorModalidad(): array;

    /** @return array{deportistas: int, modalidades: int, usuarios: int} */
    public function totales(): array;
}
