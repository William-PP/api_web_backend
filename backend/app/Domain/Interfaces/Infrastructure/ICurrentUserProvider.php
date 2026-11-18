<?php

namespace App\Domain\Interfaces\Infrastructure;

use App\Domain\Entities\User;

interface ICurrentUserProvider
{
    public function get(): ?User;

    public function id(): ?int;
}
