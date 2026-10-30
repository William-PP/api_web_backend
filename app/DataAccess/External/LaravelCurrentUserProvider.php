<?php

namespace App\DataAccess\External;

use App\DataAccess\Mappers\UserMapper;
use App\DataAccess\Models\UserModel;
use App\Domain\Entities\User;
use App\Domain\Interfaces\Infrastructure\ICurrentUserProvider;
use Illuminate\Support\Facades\Auth;

class LaravelCurrentUserProvider implements ICurrentUserProvider
{
    public function __construct(private UserMapper $mapper)
    {
    }

    public function get(): ?User
    {
        $user = Auth::user();

        return $user instanceof UserModel ? $this->mapper->toDomain($user) : null;
    }

    public function id(): ?int
    {
        return Auth::id();
    }
}
