<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

abstract class AuditBase
{
    public ?int $id = null;

    public ?DateTimeImmutable $createdAt = null;

    public ?DateTimeImmutable $updatedAt = null;
}
