<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResenasTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_vecino_puede_dejar_una_resena(): void
    {
        $user = User::factory()->create();
        $comercio = $user->comercios()->create(['nombre' => 'Almacén Don Ramón']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/comercios/{$comercio->id}/resenas", [
                'puntuacion' => 5,
                'comentario' => 'Muy buena atención.',
            ]);

        $response->assertCreated()->assertJsonPath('data.puntuacion', 5);
        $this->assertDatabaseHas('resenas', [
            'user_id' => $user->id,
            'resenable_type' => 'comercio',
            'resenable_id' => $comercio->id,
            'puntuacion' => 5,
        ]);
    }

    public function test_una_sola_resena_por_usuario_se_actualiza(): void
    {
        $user = User::factory()->create();
        $comercio = $user->comercios()->create(['nombre' => 'Kiosco La Esquina']);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/comercios/{$comercio->id}/resenas", ['puntuacion' => 3]);
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/comercios/{$comercio->id}/resenas", ['puntuacion' => 5, 'comentario' => 'Mejoró']);

        $this->assertDatabaseCount('resenas', 1);
        $this->assertDatabaseHas('resenas', ['puntuacion' => 5, 'comentario' => 'Mejoró']);
    }

    public function test_la_puntuacion_debe_estar_entre_1_y_5(): void
    {
        $user = User::factory()->create();
        $comercio = $user->comercios()->create(['nombre' => 'Farmacia Del Pueblo']);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/comercios/{$comercio->id}/resenas", ['puntuacion' => 6])
            ->assertStatus(422);
    }

    public function test_el_autor_puede_borrar_su_resena_y_otro_no(): void
    {
        $autor = User::factory()->create();
        $otro = User::factory()->create();
        $comercio = $autor->comercios()->create(['nombre' => 'Rotisería El Buen Sabor']);
        $resena = $comercio->resenas()->create(['user_id' => $autor->id, 'puntuacion' => 4]);

        $this->actingAs($otro, 'sanctum')
            ->deleteJson("/api/resenas/{$resena->id}")
            ->assertForbidden();

        $this->actingAs($autor, 'sanctum')
            ->deleteJson("/api/resenas/{$resena->id}")
            ->assertNoContent();

        $this->assertDatabaseCount('resenas', 0);
    }

    public function test_el_listado_de_resenas_es_publico_y_trae_el_promedio(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $comercio = $a->comercios()->create(['nombre' => 'Verdulería La Huerta']);
        $comercio->resenas()->create(['user_id' => $a->id, 'puntuacion' => 4]);
        $comercio->resenas()->create(['user_id' => $b->id, 'puntuacion' => 5]);

        $this->getJson("/api/comercios/{$comercio->id}/resenas")
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('promedio', 4.5);
    }

    public function test_es_mia_marca_solo_la_resena_propia(): void
    {
        $autor = User::factory()->create();
        $otro = User::factory()->create();
        $comercio = $autor->comercios()->create(['nombre' => 'Bar Central']);
        $comercio->resenas()->create(['user_id' => $autor->id, 'puntuacion' => 4]);

        $this->actingAs($autor, 'sanctum')
            ->getJson("/api/comercios/{$comercio->id}/resenas")
            ->assertOk()
            ->assertJsonPath('data.0.es_mia', true);

        $this->actingAs($otro, 'sanctum')
            ->getJson("/api/comercios/{$comercio->id}/resenas")
            ->assertOk()
            ->assertJsonPath('data.0.es_mia', false);
    }
}
