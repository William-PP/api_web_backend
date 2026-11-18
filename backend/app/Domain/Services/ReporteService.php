<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\Infrastructure\IPdfGenerator;
use App\Domain\Interfaces\Repositories\IDeportistaRepository;
use App\Domain\Interfaces\Services\IReporteService;

class ReporteService implements IReporteService
{
    public function __construct(
        private IDeportistaRepository $deportistas,
        private IPdfGenerator $pdf,
    ) {
    }

    public function deportistasPdf(): string
    {
        return $this->pdf->generate('reportes.deportistas', [
            'deportistas' => $this->deportistas->getAll(),
        ]);
    }
}
