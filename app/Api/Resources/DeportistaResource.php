<?php

namespace App\Api\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DeportistaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => $this->nombreCompleto(),
            'fecha_nacimiento' => $this->fechaNacimiento->format('Y-m-d'),
            'foto' => $this->foto,
            'foto_url' => $this->foto ? Storage::disk('public')->url($this->foto) : null,
            'modalidad_id' => $this->modalidadId,
            'modalidad' => $this->modalidad
                ? new ModalidadResource($this->modalidad)
                : null,
            'user' => $this->user
                ? new UserResource($this->user)
                : null,
            'created_at' => $this->createdAt?->format(DATE_ATOM),
            'updated_at' => $this->updatedAt?->format(DATE_ATOM),
        ];
    }
}
