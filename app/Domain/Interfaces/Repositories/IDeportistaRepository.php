<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Deportista;

/**
 * @extends IGenericRepository<Deportista>
 */
interface IDeportistaRepository extends IGenericRepository
{
    /** @return array<int, array{modalidad: string, total: int}> */
    public function countByModalidad(): array;
}
