<?php

namespace App\Domain\DTOs;

class FileData
{
    public function __construct(
        public string $contents,
        public string $originalName,
        public string $mimeType,
        public int $size,
    ) {
    }

    public function extension(): string
    {
        $extension = strtolower(pathinfo($this->originalName, PATHINFO_EXTENSION));

        if ($extension !== '') {
            return $extension;
        }

        return match ($this->mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => 'bin',
        };
    }
}
