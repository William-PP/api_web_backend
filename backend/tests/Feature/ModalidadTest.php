<?php

namespace Tests\Feature;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\DataAccess\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ModalidadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(UserModel::factory()->create());
    }

    public function test_index_lists_modalidades(): void
    {
        ModalidadModel::factory()->count(3)->create();

        $this->getJson('/api/modalidades')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'nombre', 'descripcion', 'deportistas_count']],
            ]);
    }

    public function test_store_creates_modalidad(): void
    {
        $this->postJson('/api/modalidades', [
            'nombre' => 'Futbol',
            'descripcion' => 'Deporte de equipo',
        ])->assertCreated()
            ->assertJsonPath('data.nombre', 'Futbol');

        $this->assertDatabaseHas('modalidades', ['nombre' => 'Futbol']);
    }

    public function test_store_with_duplicate_name_returns_conflict(): void
    {
        ModalidadModel::factory()->create(['nombre' => 'Futbol']);

        $this->postJson('/api/modalidades', [
            'nombre' => 'FUTBOL',
        ])->assertStatus(409);
    }

    public function test_update_modalidad(): void
    {
        $modalidad = ModalidadModel::factory()->create(['nombre' => 'Voley']);

        $this->putJson("/api/modalidades/{$modalidad->id}", [
            'nombre' => 'Voleibol',
            'descripcion' => 'Actualizada',
        ])->assertOk()
            ->assertJsonPath('data.nombre', 'Voleibol');

        $this->assertDatabaseHas('modalidades', [
            'id' => $modalidad->id,
            'nombre' => 'Voleibol',
        ]);
    }

    public function test_destroy_modalidad_without_deportistas(): void
    {
        $modalidad = ModalidadModel::factory()->create();

        $this->deleteJson("/api/modalidades/{$modalidad->id}")
            ->assertOk();

        $this->assertDatabaseMissing('modalidades', ['id' => $modalidad->id]);
    }

    public function test_destroy_modalidad_with_deportistas_returns_conflict(): void
    {
        $modalidad = ModalidadModel::factory()->create();
        DeportistaModel::factory()->for($modalidad, 'modalidad')->create();

        $this->deleteJson("/api/modalidades/{$modalidad->id}")
            ->assertStatus(409);

        $this->assertDatabaseHas('modalidades', ['id' => $modalidad->id]);
    }

    public function test_show_missing_modalidad_returns_not_found(): void
    {
        $this->getJson('/api/modalidades/9999')->assertNotFound();
    }
}
