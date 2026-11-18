<?php

namespace App\Domain\Exceptions;

class NotFoundException extends BusinessException
{
    public static function for(string $entity, int|string $id): self
    {
        return new self("No se encontró {$entity} con identificador {$id}.");
    }
}
