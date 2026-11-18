<?php

namespace Tests\Feature;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_does_not_require_authentication(): void
    {
        $modalidad = ModalidadModel::factory()->create(['nombre' => 'Ciclismo']);
        DeportistaModel::factory()->count(2)->for($modalidad, 'modalidad')->create();

        $response = $this->getJson('/api/public/deportistas');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id', 'nombre', 'apellido', 'nombre_completo',
                    'fecha_nacimiento', 'foto_url', 'modalidad',
                ]],
            ]);

        $this->assertArrayNotHasKey('user', $response->json('data.0'));
    }

    public function test_public_show_returns_single_deportista(): void
    {
        $modalidad = ModalidadModel::factory()->create(['nombre' => 'Ajedrez']);
        $deportista = DeportistaModel::factory()->for($modalidad, 'modalidad')->create();

        $this->getJson("/api/public/deportistas/{$deportista->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $deportista->id)
            ->assertJsonPath('data.modalidad', 'Ajedrez');
    }

    public function test_public_show_missing_returns_not_found(): void
    {
        $this->getJson('/api/public/deportistas/9999')->assertNotFound();
    }
}
