<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\User;

/**
 * @extends IGenericRepository<User>
 */
interface IUserRepository extends IGenericRepository
{
    public function findByEmail(string $email): ?User;
}
