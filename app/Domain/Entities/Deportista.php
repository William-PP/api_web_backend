<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class Deportista extends AuditBase
{
    public function __construct(
        public string $nombre,
        public string $apellido,
        public DateTimeImmutable $fechaNacimiento,
        public int $modalidadId,
        public int $userId,
        public ?string $foto = null,
        public ?Modalidad $modalidad = null,
        public ?User $user = null,
    ) {
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombre.' '.$this->apellido);
    }
}
