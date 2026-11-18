<?php

namespace App\DataAccess\Mappers;

use App\DataAccess\Models\UserModel;
use App\Domain\Entities\User;
use DateTimeImmutable;

class UserMapper
{
    public function toDomain(UserModel $model): User
    {
        $user = new User(
            name: $model->name,
            email: $model->email,
            passwordHash: $model->password,
            foto: $model->foto,
        );

        $user->id = $model->id;
        $user->createdAt = $model->created_at
            ? DateTimeImmutable::createFromInterface($model->created_at)
            : null;
        $user->updatedAt = $model->updated_at
            ? DateTimeImmutable::createFromInterface($model->updated_at)
            : null;

        return $user;
    }

    public function toArray(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->passwordHash,
            'foto' => $user->foto,
        ];
    }
}
