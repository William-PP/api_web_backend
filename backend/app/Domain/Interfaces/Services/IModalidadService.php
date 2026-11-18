<?php

namespace App\Domain\Interfaces\Services;

use App\Domain\Entities\Modalidad;

interface IModalidadService
{
    /** @return array<int, Modalidad> */
    public function listar(): array;

    public function obtener(int $id): Modalidad;

    public function crear(array $datos): Modalidad;

    public function actualizar(int $id, array $datos): Modalidad;

    public function eliminar(int $id): void;
}
