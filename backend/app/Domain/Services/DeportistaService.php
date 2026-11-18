<?php

namespace App\Domain\Services;

use App\Domain\DTOs\FileData;
use App\Domain\Entities\Deportista;
use App\Domain\Exceptions\BusinessException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Interfaces\Repositories\IDeportistaRepository;
use App\Domain\Interfaces\Services\IArchivoService;
use App\Domain\Interfaces\Services\IDeportistaService;
use DateTimeImmutable;

class DeportistaService implements IDeportistaService
{
    public function __construct(
        private IDeportistaRepository $deportistas,
        private IArchivoService $archivos,
    ) {
    }

    public function listar(): array
    {
        return $this->deportistas->getAll();
    }

    public function obtener(int $id): Deportista
    {
        return $this->deportistas->getById($id)
            ?? throw NotFoundException::for('el deportista', $id);
    }

    public function crear(array $datos, ?FileData $foto, int $userId): Deportista
    {
        $fechaNacimiento = new DateTimeImmutable($datos['fecha_nacimiento']);
        $this->validarFechaNacimiento($fechaNacimiento);

        $ruta = $foto ? $this->archivos->guardar($foto, 'deportistas') : null;

        $deportista = new Deportista(
            nombre: $datos['nombre'],
            apellido: $datos['apellido'],
            fechaNacimiento: $fechaNacimiento,
            modalidadId: (int) $datos['modalidad_id'],
            userId: $userId,
            foto: $ruta,
        );

        return $this->deportistas->create($deportista);
    }

    public function actualizar(int $id, array $datos, ?FileData $foto): Deportista
    {
        $deportista = $this->obtener($id);
        $fechaNacimiento = new DateTimeImmutable($datos['fecha_nacimiento']);
        $this->validarFechaNacimiento($fechaNacimiento);

        if ($foto !== null) {
            if ($deportista->foto !== null) {
                $this->archivos->eliminar($deportista->foto);
            }

            $deportista->foto = $this->archivos->guardar($foto, 'deportistas');
        }

        $deportista->nombre = $datos['nombre'];
        $deportista->apellido = $datos['apellido'];
        $deportista->fechaNacimiento = $fechaNacimiento;
        $deportista->modalidadId = (int) $datos['modalidad_id'];

        $this->deportistas->update($deportista);

        return $this->obtener($id);
    }

    public function eliminar(int $id): void
    {
        $deportista = $this->obtener($id);

        if ($deportista->foto !== null) {
            $this->archivos->eliminar($deportista->foto);
        }

        $this->deportistas->delete($id);
    }

    private function validarFechaNacimiento(DateTimeImmutable $fecha): void
    {
        if ($fecha > new DateTimeImmutable('today')) {
            throw new BusinessException('La fecha de nacimiento no puede ser futura.');
        }
    }
}
