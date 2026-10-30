<?php

namespace App\Api\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'foto' => $this->foto,
            'foto_url' => $this->foto ? Storage::disk('public')->url($this->foto) : null,
            'created_at' => $this->createdAt?->format(DATE_ATOM),
        ];
    }
}
