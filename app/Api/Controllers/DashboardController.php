<?php

namespace App\Api\Controllers;

use App\Domain\Interfaces\Services\IClimaService;
use App\Domain\Interfaces\Services\IEstadisticaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __construct(
        private IEstadisticaService $estadisticas,
        private IClimaService $clima,
    ) {
    }

    public function resumen(): JsonResponse
    {
        return response()->json([
            'totales' => $this->estadisticas->totales(),
            'por_modalidad' => $this->estadisticas->deportistasPorModalidad(),
        ]);
    }

    public function clima(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ciudad' => ['required', 'string', 'max:100'],
        ]);

        return response()->json($this->clima->obtenerClima($data['ciudad']));
    }
}
