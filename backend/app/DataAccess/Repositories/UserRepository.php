<?php

namespace App\DataAccess\Repositories;

use App\DataAccess\Mappers\UserMapper;
use App\DataAccess\Models\UserModel;
use App\Domain\Entities\User;
use App\Domain\Interfaces\Repositories\IUserRepository;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends GenericRepository implements IUserRepository
{
    protected string $modelClass = UserModel::class;

    public function __construct(private UserMapper $mapper)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $model = $this->query()->where('email', $email)->first();

        return $model ? $this->mapper->toDomain($model) : null;
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
