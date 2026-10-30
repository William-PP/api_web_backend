<?php

namespace App\Api\Controllers;

use App\Api\Resources\UserResource;
use App\Api\Support\FileDataFactory;
use App\Domain\Interfaces\Infrastructure\ICurrentUserProvider;
use App\Domain\Interfaces\Services\IPerfilService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PerfilController extends Controller
{
    public function __construct(
        private IPerfilService $perfil,
        private ICurrentUserProvider $currentUser,
    ) {
    }

    public function show(): UserResource
    {
        return new UserResource($this->perfil->obtener((int) $this->currentUser->id()));
    }

    public function update(Request $request): UserResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        return new UserResource(
            $this->perfil->actualizar((int) $this->currentUser->id(), $data),
        );
    }

    public function updateFoto(Request $request): UserResource
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        return new UserResource(
            $this->perfil->actualizarFoto(
                (int) $this->currentUser->id(),
                FileDataFactory::fromUploadedFile($request->file('foto')),
            ),
        );
    }
}
