<?php

namespace App\Domain\Interfaces\Services;

use App\Domain\DTOs\FileData;
use App\Domain\Entities\Deportista;

interface IDeportistaService
{
    /** @return array<int, Deportista> */
    public function listar(): array;

    public function obtener(int $id): Deportista;

    public function crear(array $datos, ?FileData $foto, int $userId): Deportista;

    public function actualizar(int $id, array $datos, ?FileData $foto): Deportista;

    public function eliminar(int $id): void;
}
