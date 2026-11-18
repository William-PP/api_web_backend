<?php

namespace App\Domain\Interfaces\Infrastructure;

use App\Domain\DTOs\FileData;

interface IFileStorage
{
    public function save(FileData $file, string $folder): string;

    public function delete(string $path): void;
}
