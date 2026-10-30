<?php

namespace Tests\Feature;

use App\DataAccess\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'juan@example.com')
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'foto', 'foto_url', 'created_at'],
                'token',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_register_with_duplicate_email_returns_conflict(): void
    {
        UserModel::factory()->create(['email' => 'dup@example.com']);

        $this->postJson('/api/register', [
            'name' => 'Otro',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(409);
    }

    public function test_login_with_valid_credentials_returns_token(): void
    {
        UserModel::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_login_with_invalid_credentials_returns_unprocessable(): void
    {
        UserModel::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'incorrecta',
        ])->assertStatus(422);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = UserModel::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = UserModel::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_forgot_password_stores_reset_token(): void
    {
        UserModel::factory()->create(['email' => 'forgot@example.com']);

        $this->postJson('/api/forgot-password', [
            'email' => 'forgot@example.com',
        ])->assertOk();

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'forgot@example.com',
        ]);
    }

    public function test_reset_password_updates_password(): void
    {
        $user = UserModel::factory()->create([
            'email' => 'reset@example.com',
            'password' => 'oldpassword',
        ]);

        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'reset@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertOk();

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
