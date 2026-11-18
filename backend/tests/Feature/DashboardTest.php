<?php

namespace Tests\Feature;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\DataAccess\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_resumen_returns_totals_and_chart_data(): void
    {
        $user = UserModel::factory()->create();
        Sanctum::actingAs($user);

        $modalidad = ModalidadModel::factory()->create(['nombre' => 'Natacion']);
        DeportistaModel::factory()->count(3)->for($modalidad, 'modalidad')->for($user, 'user')->create();

        $this->getJson('/api/dashboard/resumen')
            ->assertOk()
            ->assertJsonPath('totales.deportistas', 3)
            ->assertJsonPath('totales.modalidades', 1)
            ->assertJsonPath('totales.usuarios', 1)
            ->assertJsonPath('por_modalidad.0.modalidad', 'Natacion')
            ->assertJsonPath('por_modalidad.0.total', 3);
    }

    public function test_clima_returns_weather_from_third_party_api(): void
    {
        Sanctum::actingAs(UserModel::factory()->create());

        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
                'results' => [[
                    'name' => 'Madrid',
                    'country' => 'España',
                    'latitude' => 40.4168,
                    'longitude' => -3.7038,
                ]],
            ]),
            'api.open-meteo.com/*' => Http::response([
                'current' => [
                    'temperature_2m' => 21.5,
                    'relative_humidity_2m' => 45,
                    'wind_speed_10m' => 12.3,
                    'weather_code' => 1,
                    'time' => '2026-10-30T12:00',
                ],
            ]),
        ]);

        $this->getJson('/api/dashboard/clima?ciudad=Madrid')
            ->assertOk()
            ->assertJsonPath('ciudad', 'Madrid')
            ->assertJsonPath('temperatura', 21.5)
            ->assertJsonPath('humedad', 45);
    }

    public function test_clima_with_unknown_city_returns_unprocessable(): void
    {
        Sanctum::actingAs(UserModel::factory()->create());

        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(['results' => []]),
        ]);

        $this->getJson('/api/dashboard/clima?ciudad=CiudadInexistente')
            ->assertStatus(422);
    }

    public function test_clima_requires_city_parameter(): void
    {
        Sanctum::actingAs(UserModel::factory()->create());

        $this->getJson('/api/dashboard/clima')->assertStatus(422);
    }
}
