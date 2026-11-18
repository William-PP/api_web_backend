<?php

namespace App\Domain\Services;

use App\Domain\DTOs\FileData;
use App\Domain\Interfaces\Infrastructure\IFileStorage;
use App\Domain\Interfaces\Services\IArchivoService;

class ArchivoService implements IArchivoService
{
    public function __construct(private IFileStorage $storage)
    {
    }

    public function guardar(FileData $archivo, string $carpeta): string
    {
        return $this->storage->save($archivo, $carpeta);
    }

    public function eliminar(string $ruta): void
    {
        $this->storage->delete($ruta);
    }
}
