<?php

namespace Tests\Feature;

use App\Domain\Categorias\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiciosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_vecino_autenticado_puede_publicar_un_servicio(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create(['modulo' => 'servicios']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/servicios', [
            'categoria_id' => $categoria->id,
            'titulo' => 'Electricista matriculado',
            'whatsapp' => '381500000',
            'zona' => 'Centro',
        ]);

        $response->assertCreated()->assertJsonPath('data.titulo', 'Electricista matriculado');
        $this->assertDatabaseHas('servicios', ['titulo' => 'Electricista matriculado', 'user_id' => $user->id]);
    }

    public function test_el_listado_de_servicios_es_publico(): void
    {
        $this->getJson('/api/servicios')->assertOk();
    }
}
