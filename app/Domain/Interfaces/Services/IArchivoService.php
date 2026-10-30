<?php

namespace App\Domain\Interfaces\Services;

use App\Domain\DTOs\FileData;

interface IArchivoService
{
    public function guardar(FileData $archivo, string $carpeta): string;

    public function eliminar(string $ruta): void;
}
