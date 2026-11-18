<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Modalidad;

/**
 * @extends IGenericRepository<Modalidad>
 */
interface IModalidadRepository extends IGenericRepository
{
    public function findByNombre(string $nombre): ?Modalidad;

    public function hasDeportistas(int $modalidadId): bool;
}
