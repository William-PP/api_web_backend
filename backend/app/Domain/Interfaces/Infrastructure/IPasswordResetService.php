<?php

namespace App\Domain\Interfaces\Infrastructure;

interface IPasswordResetService
{
    public function sendResetLink(string $email): string;

    public function reset(array $data): void;
}
