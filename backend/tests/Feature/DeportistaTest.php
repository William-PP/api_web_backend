<?php

namespace Tests\Feature;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\DataAccess\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeportistaTest extends TestCase
{
    use RefreshDatabase;

    private UserModel $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserModel::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_index_lists_deportistas_with_modalidad(): void
    {
        $modalidad = ModalidadModel::factory()->create(['nombre' => 'Atletismo']);
        DeportistaModel::factory()->count(2)->for($modalidad, 'modalidad')->for($this->user, 'user')->create();

        $this->getJson('/api/deportistas')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.modalidad.nombre', 'Atletismo')
            ->assertJsonStructure([
                'data' => [[
                    'id', 'nombre', 'apellido', 'nombre_completo', 'fecha_nacimiento',
                    'foto', 'foto_url', 'modalidad_id', 'modalidad', 'user',
                ]],
            ]);
    }

    public function test_store_creates_deportista_with_photo(): void
    {
        Storage::fake('public');
        $modalidad = ModalidadModel::factory()->create();

        $response = $this->post('/api/deportistas', [
            'nombre' => 'Ana',
            'apellido' => 'Gomez',
            'fecha_nacimiento' => '2000-05-10',
            'modalidad_id' => $modalidad->id,
            'foto' => $this->fakeImage('ana.png'),
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('data.nombre', 'Ana')
            ->assertJsonPath('data.modalidad_id', $modalidad->id);

        Storage::disk('public')->assertExists($response->json('data.foto'));

        $this->assertDatabaseHas('deportistas', [
            'nombre' => 'Ana',
            'apellido' => 'Gomez',
            'modalidad_id' => $modalidad->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_with_future_birth_date_returns_unprocessable(): void
    {
        $modalidad = ModalidadModel::factory()->create();

        $this->postJson('/api/deportistas', [
            'nombre' => 'Ana',
            'apellido' => 'Gomez',
            'fecha_nacimiento' => now()->addDay()->format('Y-m-d'),
            'modalidad_id' => $modalidad->id,
        ])->assertStatus(422);
    }

    public function test_store_with_missing_modalidad_returns_unprocessable(): void
    {
        $this->postJson('/api/deportistas', [
            'nombre' => 'Ana',
            'apellido' => 'Gomez',
            'fecha_nacimiento' => '2000-05-10',
            'modalidad_id' => 9999,
        ])->assertStatus(422);
    }

    public function test_update_deportista(): void
    {
        $modalidad = ModalidadModel::factory()->create();
        $deportista = DeportistaModel::factory()->for($modalidad, 'modalidad')->for($this->user, 'user')->create();

        $this->putJson("/api/deportistas/{$deportista->id}", [
            'nombre' => 'Carlos',
            'apellido' => 'Lopez',
            'fecha_nacimiento' => '1998-01-01',
            'modalidad_id' => $modalidad->id,
        ])->assertOk()
            ->assertJsonPath('data.nombre', 'Carlos');

        $this->assertDatabaseHas('deportistas', [
            'id' => $deportista->id,
            'nombre' => 'Carlos',
            'apellido' => 'Lopez',
        ]);
    }

    public function test_destroy_deportista(): void
    {
        $deportista = DeportistaModel::factory()->for($this->user, 'user')->create();

        $this->deleteJson("/api/deportistas/{$deportista->id}")
            ->assertOk();

        $this->assertDatabaseMissing('deportistas', ['id' => $deportista->id]);
    }

    public function test_show_missing_deportista_returns_not_found(): void
    {
        $this->getJson('/api/deportistas/9999')->assertNotFound();
    }
}
