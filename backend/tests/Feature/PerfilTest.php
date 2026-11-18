<?php

namespace Tests\Feature;

use App\DataAccess\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PerfilTest extends TestCase
{
    use RefreshDatabase;

    private UserModel $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserModel::factory()->create([
            'name' => 'Usuario Original',
            'email' => 'perfil@example.com',
        ]);
        Sanctum::actingAs($this->user);
    }

    public function test_show_returns_profile(): void
    {
        $this->getJson('/api/perfil')
            ->assertOk()
            ->assertJsonPath('data.name', 'Usuario Original')
            ->assertJsonPath('data.email', 'perfil@example.com');
    }

    public function test_update_profile(): void
    {
        $this->putJson('/api/perfil', [
            'name' => 'Usuario Editado',
            'email' => 'editado@example.com',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Usuario Editado')
            ->assertJsonPath('data.email', 'editado@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Usuario Editado',
            'email' => 'editado@example.com',
        ]);
    }

    public function test_update_profile_with_used_email_returns_conflict(): void
    {
        UserModel::factory()->create(['email' => 'ocupado@example.com']);

        $this->putJson('/api/perfil', [
            'name' => 'Usuario Editado',
            'email' => 'ocupado@example.com',
        ])->assertStatus(409);
    }

    public function test_update_photo(): void
    {
        Storage::fake('public');

        $response = $this->post('/api/perfil/foto', [
            'foto' => $this->fakeImage(),
        ], ['Accept' => 'application/json']);

        $response->assertOk();

        Storage::disk('public')->assertExists($response->json('data.foto'));
        $this->assertNotNull($response->json('data.foto_url'));
    }
}
