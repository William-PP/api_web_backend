<?php

namespace App\DataAccess\Repositories;

use App\DataAccess\Mappers\ModalidadMapper;
use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\Domain\Entities\Modalidad;
use App\Domain\Interfaces\Repositories\IModalidadRepository;
use Illuminate\Database\Eloquent\Model;

class ModalidadRepository extends GenericRepository implements IModalidadRepository
{
    protected string $modelClass = ModalidadModel::class;

    protected array $withCount = ['deportistas'];

    public function __construct(private ModalidadMapper $mapper)
    {
    }

    public function findByNombre(string $nombre): ?Modalidad
    {
        $model = $this->query()
            ->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])
            ->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function hasDeportistas(int $modalidadId): bool
    {
        return DeportistaModel::where('modalidad_id', $modalidadId)->exists();
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
