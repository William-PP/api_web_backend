<?php

namespace App\Domain\Services;

use App\Domain\Entities\Modalidad;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Interfaces\Repositories\IModalidadRepository;
use App\Domain\Interfaces\Services\IModalidadService;

class ModalidadService implements IModalidadService
{
    public function __construct(private IModalidadRepository $modalidades)
    {
    }

    public function listar(): array
    {
        return $this->modalidades->getAll();
    }

    public function obtener(int $id): Modalidad
    {
        return $this->modalidades->getById($id)
            ?? throw NotFoundException::for('la modalidad', $id);
    }

    public function crear(array $datos): Modalidad
    {
        $this->validarNombreUnico($datos['nombre']);

        return $this->modalidades->create(new Modalidad(
            nombre: $datos['nombre'],
            descripcion: $datos['descripcion'] ?? null,
        ));
    }

    public function actualizar(int $id, array $datos): Modalidad
    {
        $modalidad = $this->obtener($id);
        $this->validarNombreUnico($datos['nombre'], $id);

        $modalidad->nombre = $datos['nombre'];
        $modalidad->descripcion = $datos['descripcion'] ?? null;

        $this->modalidades->update($modalidad);

        return $this->obtener($id);
    }

    public function eliminar(int $id): void
    {
        $this->obtener($id);

        if ($this->modalidades->hasDeportistas($id)) {
            throw new ConflictException('No se puede eliminar una modalidad que tiene deportistas asociados.');
        }

        $this->modalidades->delete($id);
    }

    private function validarNombreUnico(string $nombre, ?int $ignoreId = null): void
    {
        $existente = $this->modalidades->findByNombre($nombre);

        if ($existente !== null && $existente->id !== $ignoreId) {
            throw new ConflictException('Ya existe una modalidad con ese nombre.');
        }
    }
}
