<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\Repositories\IDeportistaRepository;
use App\Domain\Interfaces\Repositories\IModalidadRepository;
use App\Domain\Interfaces\Repositories\IUserRepository;
use App\Domain\Interfaces\Services\IEstadisticaService;

class EstadisticaService implements IEstadisticaService
{
    public function __construct(
        private IDeportistaRepository $deportistas,
        private IModalidadRepository $modalidades,
        private IUserRepository $usuarios,
    ) {
    }

    public function deportistasPorModalidad(): array
    {
        return $this->deportistas->countByModalidad();
    }

    public function totales(): array
    {
        return [
            'deportistas' => $this->deportistas->count(),
            'modalidades' => $this->modalidades->count(),
            'usuarios' => $this->usuarios->count(),
        ];
    }
}
