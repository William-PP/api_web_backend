<?php

namespace App\Domain\Interfaces\Repositories;

/**
 * @template T
 */
interface IGenericRepository
{
    /** @return array<int, T> */
    public function getAll(): array;

    /** @return T|null */
    public function getById(int $id): mixed;

    /** @param T $entity
     *  @return T */
    public function create(mixed $entity): mixed;

    /** @param T $entity */
    public function update(mixed $entity): void;

    public function delete(int $id): void;

    public function exists(int $id): bool;

    public function count(): int;
}
