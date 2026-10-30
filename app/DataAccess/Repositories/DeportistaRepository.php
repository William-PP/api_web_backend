<?php

namespace App\DataAccess\Repositories;

use App\DataAccess\Mappers\DeportistaMapper;
use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\Domain\Entities\Deportista;
use App\Domain\Interfaces\Repositories\IDeportistaRepository;
use Illuminate\Database\Eloquent\Model;

class DeportistaRepository extends GenericRepository implements IDeportistaRepository
{
    protected string $modelClass = DeportistaModel::class;

    protected array $with = ['modalidad', 'user'];

    public function __construct(private DeportistaMapper $mapper)
    {
    }

    public function countByModalidad(): array
    {
        return ModalidadModel::withCount('deportistas')
            ->orderBy('nombre')
            ->get()
            ->map(fn (ModalidadModel $modalidad) => [
                'modalidad' => $modalidad->nombre,
                'total' => (int) $modalidad->deportistas_count,
            ])
            ->all();
    }

    protected function toDomain(Model $model): object
    {
        return $this->mapper->toDomain($model);
    }

    protected function toArray(object $entity): array
    {
        return $this->mapper->toArray($entity);
    }
}
