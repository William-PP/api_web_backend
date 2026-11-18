<?php

namespace App\Domain\Services;

use App\Domain\DTOs\FileData;
use App\Domain\Entities\User;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Interfaces\Repositories\IUserRepository;
use App\Domain\Interfaces\Services\IArchivoService;
use App\Domain\Interfaces\Services\IPerfilService;

class PerfilService implements IPerfilService
{
    public function __construct(
        private IUserRepository $usuarios,
        private IArchivoService $archivos,
    ) {
    }

    public function obtener(int $userId): User
    {
        return $this->usuarios->getById($userId)
            ?? throw NotFoundException::for('el usuario', $userId);
    }

    public function actualizar(int $userId, array $datos): User
    {
        $usuario = $this->obtener($userId);

        $existente = $this->usuarios->findByEmail($datos['email']);
        if ($existente !== null && $existente->id !== $userId) {
            throw new ConflictException('Ya existe un usuario con ese correo.');
        }

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];

        $this->usuarios->update($usuario);

        return $this->obtener($userId);
    }

    public function actualizarFoto(int $userId, FileData $foto): User
    {
        $usuario = $this->obtener($userId);

        if ($usuario->foto !== null) {
            $this->archivos->eliminar($usuario->foto);
        }

        $usuario->foto = $this->archivos->guardar($foto, 'perfiles');

        $this->usuarios->update($usuario);

        return $this->obtener($userId);
    }
}
