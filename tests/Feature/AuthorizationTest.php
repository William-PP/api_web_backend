<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_endpoints_require_authentication(): void
    {
        $this->getJson('/api/deportistas')->assertUnauthorized();
        $this->getJson('/api/modalidades')->assertUnauthorized();
        $this->getJson('/api/dashboard/resumen')->assertUnauthorized();
        $this->getJson('/api/dashboard/clima?ciudad=Madrid')->assertUnauthorized();
        $this->getJson('/api/perfil')->assertUnauthorized();
        $this->postJson('/api/logout')->assertUnauthorized();
        $this->get('/api/reportes/deportistas.pdf')->assertUnauthorized();
    }
}
