<?php

namespace App\Api\Controllers;

use App\Domain\Entities\Deportista;
use App\Domain\Interfaces\Services\IDeportistaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class PublicDeportistaController extends Controller
{
    public function __construct(private IDeportistaService $deportistas)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => array_map(
                fn (Deportista $deportista) => $this->toArray($deportista),
                $this->deportistas->listar(),
            ),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => $this->toArray($this->deportistas->obtener($id)),
        ]);
    }

    private function toArray(Deportista $deportista): array
    {
        return [
            'id' => $deportista->id,
            'nombre' => $deportista->nombre,
            'apellido' => $deportista->apellido,
            'nombre_completo' => $deportista->nombreCompleto(),
            'fecha_nacimiento' => $deportista->fechaNacimiento->format('Y-m-d'),
            'foto_url' => $deportista->foto
                ? Storage::disk('public')->url($deportista->foto)
                : null,
            'modalidad' => $deportista->modalidad?->nombre,
        ];
    }
}
