<?php

namespace App\Domain\Services;

use App\Domain\Entities\User;
use App\Domain\Exceptions\BusinessException;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Interfaces\Infrastructure\ICurrentUserProvider;
use App\Domain\Interfaces\Infrastructure\IPasswordHasher;
use App\Domain\Interfaces\Infrastructure\IPasswordResetService;
use App\Domain\Interfaces\Infrastructure\ITokenService;
use App\Domain\Interfaces\Repositories\IUserRepository;
use App\Domain\Interfaces\Services\IAuthService;

class AuthService implements IAuthService
{
    public function __construct(
        private IUserRepository $users,
        private IPasswordHasher $hasher,
        private ITokenService $tokens,
        private IPasswordResetService $passwordReset,
        private ICurrentUserProvider $currentUser,
    ) {
    }

    public function register(array $data): array
    {
        if ($this->users->findByEmail($data['email']) !== null) {
            throw new ConflictException('Ya existe un usuario con ese correo.');
        }

        $user = new User(
            name: $data['name'],
            email: $data['email'],
            passwordHash: $this->hasher->hash($data['password']),
        );

        $user = $this->users->create($user);

        return [
            'user' => $user,
            'token' => $this->tokens->createToken($user),
        ];
    }

    public function login(array $data): array
    {
        $user = $this->users->findByEmail($data['email']);

        if ($user === null || ! $this->hasher->verify($data['password'], $user->passwordHash)) {
            throw new BusinessException('Credenciales incorrectas.');
        }

        return [
            'user' => $user,
            'token' => $this->tokens->createToken($user),
        ];
    }

    public function logout(): void
    {
        $this->tokens->revokeCurrentToken();
    }

    public function me(): User
    {
        return $this->currentUser->get()
            ?? throw new NotFoundException('No hay un usuario autenticado.');
    }

    public function forgotPassword(string $email): string
    {
        return $this->passwordReset->sendResetLink($email);
    }

    public function resetPassword(array $data): void
    {
        $this->passwordReset->reset($data);
    }
}
