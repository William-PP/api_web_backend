<?php

namespace App\DataAccess\External;

use App\Domain\Interfaces\Infrastructure\IPasswordHasher;
use Illuminate\Support\Facades\Hash;

class LaravelPasswordHasher implements IPasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return Hash::check($plainPassword, $passwordHash);
    }
}
