<?php

namespace App\Api\Requests;

use App\Api\Support\FileDataFactory;
use App\Domain\DTOs\FileData;
use Illuminate\Foundation\Http\FormRequest;

class DeportistaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'modalidad_id' => ['required', 'integer', 'exists:modalidades,id'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function fotoData(): ?FileData
    {
        $file = $this->file('foto');

        return $file ? FileDataFactory::fromUploadedFile($file) : null;
    }
}
