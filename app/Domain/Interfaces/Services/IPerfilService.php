<?php

namespace App\Domain\Interfaces\Services;

use App\Domain\DTOs\FileData;
use App\Domain\Entities\User;

interface IPerfilService
{
    public function obtener(int $userId): User;

    public function actualizar(int $userId, array $datos): User;

    public function actualizarFoto(int $userId, FileData $foto): User;
}
