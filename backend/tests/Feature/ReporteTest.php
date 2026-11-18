<?php

namespace Tests\Feature;

use App\DataAccess\Models\DeportistaModel;
use App\DataAccess\Models\ModalidadModel;
use App\DataAccess\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_deportistas_pdf_is_generated(): void
    {
        Sanctum::actingAs(UserModel::factory()->create());

        $modalidad = ModalidadModel::factory()->create(['nombre' => 'Tenis']);
        DeportistaModel::factory()->for($modalidad, 'modalidad')->create([
            'nombre' => 'Lucia',
            'apellido' => 'Martinez',
        ]);

        $response = $this->get('/api/reportes/deportistas.pdf');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }
}
