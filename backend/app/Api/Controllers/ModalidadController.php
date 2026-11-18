<?php

namespace App\Api\Controllers;

use App\Api\Requests\ModalidadRequest;
use App\Api\Resources\ModalidadResource;
use App\Domain\Interfaces\Services\IModalidadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ModalidadController extends Controller
{
    public function __construct(private IModalidadService $modalidades)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        return ModalidadResource::collection($this->modalidades->listar());
    }

    public function store(ModalidadRequest $request): JsonResponse
    {
        $modalidad = $this->modalidades->crear($request->validated());

        return (new ModalidadResource($modalidad))->response()->setStatusCode(201);
    }

    public function show(int $id): ModalidadResource
    {
        return new ModalidadResource($this->modalidades->obtener($id));
    }

    public function update(ModalidadRequest $request, int $id): ModalidadResource
    {
        return new ModalidadResource(
            $this->modalidades->actualizar($id, $request->validated()),
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->modalidades->eliminar($id);

        return response()->json(['message' => 'Modalidad eliminada correctamente.']);
    }
}
