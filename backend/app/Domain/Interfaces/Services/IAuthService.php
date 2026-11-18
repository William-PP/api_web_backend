<?php

namespace App\Domain\Interfaces\Services;

use App\Domain\Entities\User;

interface IAuthService
{
    /** @return array{user: User, token: string} */
    public function register(array $data): array;

    /** @return array{user: User, token: string} */
    public function login(array $data): array;

    public function logout(): void;

    public function me(): User;

    public function forgotPassword(string $email): string;

    public function resetPassword(array $data): void;
}
