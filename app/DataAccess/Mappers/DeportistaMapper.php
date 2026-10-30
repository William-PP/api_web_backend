<?php

namespace App\DataAccess\Mappers;

use App\DataAccess\Models\DeportistaModel;
use App\Domain\Entities\Deportista;
use DateTimeImmutable;

class DeportistaMapper
{
    public function __construct(
        private ModalidadMapper $modalidadMapper,
        private UserMapper $userMapper,
    ) {
    }

    public function toDomain(DeportistaModel $model): Deportista
    {
        $deportista = new Deportista(
            nombre: $model->nombre,
            apellido: $model->apellido,
            fechaNacimiento: DateTimeImmutable::createFromInterface($model->fecha_nacimiento),
            modalidadId: $model->modalidad_id,
            userId: $model->user_id,
            foto: $model->foto,
            modalidad: $model->relationLoaded('modalidad') && $model->modalidad
                ? $this->modalidadMapper->toDomain($model->modalidad)
                : null,
            user: $model->relationLoaded('user') && $model->user
                ? $this->userMapper->toDomain($model->user)
                : null,
        );

        $deportista->id = $model->id;
        $deportista->createdAt = $model->created_at
            ? DateTimeImmutable::createFromInterface($model->created_at)
            : null;
        $deportista->updatedAt = $model->updated_at
            ? DateTimeImmutable::createFromInterface($model->updated_at)
            : null;

        return $deportista;
    }

    public function toArray(Deportista $deportista): array
    {
        return [
            'nombre' => $deportista->nombre,
            'apellido' => $deportista->apellido,
            'fecha_nacimiento' => $deportista->fechaNacimiento->format('Y-m-d'),
            'foto' => $deportista->foto,
            'modalidad_id' => $deportista->modalidadId,
            'user_id' => $deportista->userId,
        ];
    }
}
