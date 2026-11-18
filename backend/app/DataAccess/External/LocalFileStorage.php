<?php

namespace App\DataAccess\External;

use App\Domain\DTOs\FileData;
use App\Domain\Interfaces\Infrastructure\IFileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalFileStorage implements IFileStorage
{
    public function save(FileData $file, string $folder): string
    {
        $path = trim($folder, '/').'/'.Str::uuid().'.'.$file->extension();

        Storage::disk('public')->put($path, $file->contents);

        return $path;
    }

    public function delete(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
