<?php

namespace App\DataAccess\Mappers;

use App\DataAccess\Models\ModalidadModel;
use App\Domain\Entities\Modalidad;
use DateTimeImmutable;

class ModalidadMapper
{
    public function toDomain(ModalidadModel $model): Modalidad
    {
        $modalidad = new Modalidad(
            nombre: $model->nombre,
            descripcion: $model->descripcion,
            deportistasCount: $model->deportistas_count ?? null,
        );

        $modalidad->id = $model->id;
        $modalidad->createdAt = $model->created_at
            ? DateTimeImmutable::createFromInterface($model->created_at)
            : null;
        $modalidad->updatedAt = $model->updated_at
            ? DateTimeImmutable::createFromInterface($model->updated_at)
            : null;

        return $modalidad;
    }

    public function toArray(Modalidad $modalidad): array
    {
        return [
            'nombre' => $modalidad->nombre,
            'descripcion' => $modalidad->descripcion,
        ];
    }
}
