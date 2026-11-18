<?php

namespace App\Api\Controllers;

use App\Api\Requests\DeportistaRequest;
use App\Api\Resources\DeportistaResource;
use App\Domain\Interfaces\Infrastructure\ICurrentUserProvider;
use App\Domain\Interfaces\Services\IDeportistaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class DeportistaController extends Controller
{
    public function __construct(
        private IDeportistaService $deportistas,
        private ICurrentUserProvider $currentUser,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        return DeportistaResource::collection($this->deportistas->listar());
    }

    public function store(DeportistaRequest $request): JsonResponse
    {
        $deportista = $this->deportistas->crear(
            $request->validated(),
            $request->fotoData(),
            (int) $this->currentUser->id(),
        );

        return (new DeportistaResource($deportista))->response()->setStatusCode(201);
    }

    public function show(int $id): DeportistaResource
    {
        return new DeportistaResource($this->deportistas->obtener($id));
    }

    public function update(DeportistaRequest $request, int $id): DeportistaResource
    {
        $deportista = $this->deportistas->actualizar(
            $id,
            $request->validated(),
            $request->fotoData(),
        );

        return new DeportistaResource($deportista);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deportistas->eliminar($id);

        return response()->json(['message' => 'Deportista eliminado correctamente.']);
    }
}
