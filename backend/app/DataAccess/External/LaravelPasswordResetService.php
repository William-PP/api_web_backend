<?php

namespace App\DataAccess\External;

use App\Domain\Exceptions\BusinessException;
use App\Domain\Interfaces\Infrastructure\IPasswordResetService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class LaravelPasswordResetService implements IPasswordResetService
{
    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new BusinessException(__($status));
        }

        return __($status);
    }

    public function reset(array $data): void
    {
        $status = Password::reset($data, function ($user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new BusinessException(__($status));
        }
    }
}
