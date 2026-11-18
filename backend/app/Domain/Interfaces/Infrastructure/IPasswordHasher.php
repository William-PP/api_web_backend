<?php

namespace App\Domain\Interfaces\Infrastructure;

interface IPasswordHasher
{
    public function hash(string $plainPassword): string;

    public function verify(string $plainPassword, string $passwordHash): bool;
}
