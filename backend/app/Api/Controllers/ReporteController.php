<?php

namespace App\Api\Controllers;

use App\Domain\Interfaces\Services\IReporteService;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ReporteController extends Controller
{
    public function __construct(private IReporteService $reportes)
    {
    }

    public function deportistas(): Response
    {
        return response($this->reportes->deportistasPdf(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="deportistas.pdf"',
        ]);
    }
}
