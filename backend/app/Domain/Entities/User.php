<?php

namespace App\Domain\Entities;

class User extends AuditBase
{
    public function __construct(
        public string $name,
        public string $email,
        public string $passwordHash,
        public ?string $foto = null,
    ) {
    }
}
