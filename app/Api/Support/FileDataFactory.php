<?php

namespace App\Api\Support;

use App\Domain\DTOs\FileData;
use Illuminate\Http\UploadedFile;

class FileDataFactory
{
    public static function fromUploadedFile(UploadedFile $file): FileData
    {
        return new FileData(
            contents: $file->getContent(),
            originalName: $file->getClientOriginalName(),
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            size: $file->getSize(),
        );
    }
}
