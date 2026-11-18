<?php

namespace App\Domain\Interfaces\Infrastructure;

use App\Domain\Entities\User;

interface ITokenService
{
    public function createToken(User $user): string;

    public function revokeCurrentToken(): void;
}
