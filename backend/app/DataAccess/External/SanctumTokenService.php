<?php

namespace App\DataAccess\External;

use App\DataAccess\Models\UserModel;
use App\Domain\Entities\User;
use App\Domain\Interfaces\Infrastructure\ITokenService;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenService implements ITokenService
{
    public function createToken(User $user): string
    {
        $model = UserModel::findOrFail($user->id);

        return $model->createToken('api')->plainTextToken;
    }

    public function revokeCurrentToken(): void
    {
        $token = Auth::user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }
}
