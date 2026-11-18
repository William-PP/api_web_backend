<?php

namespace App\DataAccess\Providers;

use App\DataAccess\External\DomPdfGenerator;
use App\DataAccess\External\LaravelCurrentUserProvider;
use App\DataAccess\External\LaravelPasswordHasher;
use App\DataAccess\External\LaravelPasswordResetService;
use App\DataAccess\External\LocalFileStorage;
use App\DataAccess\External\OpenMeteoClient;
use App\DataAccess\External\SanctumTokenService;
use App\DataAccess\Repositories\DeportistaRepository;
use App\DataAccess\Repositories\ModalidadRepository;
use App\DataAccess\Repositories\UserRepository;
use App\Domain\Interfaces\Infrastructure\ICurrentUserProvider;
use App\Domain\Interfaces\Infrastructure\IFileStorage;
use App\Domain\Interfaces\Infrastructure\IPasswordHasher;
use App\Domain\Interfaces\Infrastructure\IPasswordResetService;
use App\Domain\Interfaces\Infrastructure\IPdfGenerator;
use App\Domain\Interfaces\Infrastructure\ITokenService;
use App\Domain\Interfaces\Infrastructure\IWeatherClient;
use App\Domain\Interfaces\Repositories\IDeportistaRepository;
use App\Domain\Interfaces\Repositories\IModalidadRepository;
use App\Domain\Interfaces\Repositories\IUserRepository;
use App\Domain\Interfaces\Services\IArchivoService;
use App\Domain\Interfaces\Services\IAuthService;
use App\Domain\Interfaces\Services\IClimaService;
use App\Domain\Interfaces\Services\IDeportistaService;
use App\Domain\Interfaces\Services\IEstadisticaService;
use App\Domain\Interfaces\Services\IModalidadService;
use App\Domain\Interfaces\Services\IPerfilService;
use App\Domain\Interfaces\Services\IReporteService;
use App\Domain\Services\ArchivoService;
use App\Domain\Services\AuthService;
use App\Domain\Services\ClimaService;
use App\Domain\Services\DeportistaService;
use App\Domain\Services\EstadisticaService;
use App\Domain\Services\ModalidadService;
use App\Domain\Services\PerfilService;
use App\Domain\Services\ReporteService;
use Illuminate\Support\ServiceProvider;

class DataAccessServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IModalidadRepository::class, ModalidadRepository::class);
        $this->app->bind(IDeportistaRepository::class, DeportistaRepository::class);

        $this->app->bind(IFileStorage::class, LocalFileStorage::class);
        $this->app->bind(ITokenService::class, SanctumTokenService::class);
        $this->app->bind(IPasswordHasher::class, LaravelPasswordHasher::class);
        $this->app->bind(IPdfGenerator::class, DomPdfGenerator::class);
        $this->app->bind(IWeatherClient::class, OpenMeteoClient::class);
        $this->app->bind(IPasswordResetService::class, LaravelPasswordResetService::class);
        $this->app->bind(ICurrentUserProvider::class, LaravelCurrentUserProvider::class);

        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IDeportistaService::class, DeportistaService::class);
        $this->app->bind(IModalidadService::class, ModalidadService::class);
        $this->app->bind(IEstadisticaService::class, EstadisticaService::class);
        $this->app->bind(IPerfilService::class, PerfilService::class);
        $this->app->bind(IReporteService::class, ReporteService::class);
        $this->app->bind(IArchivoService::class, ArchivoService::class);
        $this->app->bind(IClimaService::class, ClimaService::class);
    }
}
