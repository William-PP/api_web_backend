<?php

namespace App\DataAccess\Repositories;

use App\Domain\Interfaces\Repositories\IGenericRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class GenericRepository implements IGenericRepository
{
    /** @var class-string<Model> */
    protected string $modelClass;

    /** @var array<int, string> */
    protected array $with = [];

    /** @var array<int, string> */
    protected array $withCount = [];

    abstract protected function toDomain(Model $model): object;

    abstract protected function toArray(object $entity): array;

    protected function query(): Builder
    {
        $query = $this->modelClass::query();

        if ($this->with !== []) {
            $query->with($this->with);
        }

        if ($this->withCount !== []) {
            $query->withCount($this->withCount);
        }

        return $query;
    }

    public function getAll(): array
    {
        return $this->query()
            ->get()
            ->map(fn (Model $model) => $this->toDomain($model))
            ->all();
    }

    public function getById(int $id): mixed
    {
        $model = $this->query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function create(mixed $entity): mixed
    {
        $model = $this->modelClass::create($this->toArray($entity));

        if ($this->with !== []) {
            $model->load($this->with);
        }

        if ($this->withCount !== []) {
            $model->loadCount($this->withCount);
        }

        return $this->toDomain($model);
    }

    public function update(mixed $entity): void
    {
        $model = $this->modelClass::findOrFail($entity->id);

        $model->update($this->toArray($entity));
    }

    public function delete(int $id): void
    {
        $this->modelClass::whereKey($id)->delete();
    }

    public function exists(int $id): bool
    {
        return $this->modelClass::whereKey($id)->exists();
    }

    public function count(): int
    {
        return $this->modelClass::count();
    }
}
