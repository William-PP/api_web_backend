<?php

namespace App\Domain\Entities;

class Modalidad extends AuditBase
{
    public function __construct(
        public string $nombre,
        public ?string $descripcion = null,
        public ?int $deportistasCount = null,
    ) {
    }
}
