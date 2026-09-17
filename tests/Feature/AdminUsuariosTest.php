<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_vecino_no_puede_entrar_al_panel_de_usuarios(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson('/api/admin/usuarios')->assertForbidden();
        $this->actingAs($user, 'sanctum')->getJson('/api/admin/resumen')->assertForbidden();
    }

    public function test_el_admin_lista_las_cuentas_con_sus_publicaciones(): void
    {
        $admin = User::factory()->admin()->create();
        $vecino = User::factory()->create(['nombre' => 'Vecina Test']);
        $vecino->comercios()->create(['nombre' => 'Almacén']);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/usuarios')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/admin/usuarios/{$vecino->id}")
            ->assertOk()
            ->assertJsonPath('data.nombre', 'Vecina Test')
            ->assertJsonPath('data.publicaciones.comercios', 1)
            ->assertJsonCount(1, 'data.comercios');
    }

    public function test_suspender_bloquea_el_login_y_cierra_las_sesiones(): void
    {
        $admin = User::factory()->admin()->create();
        $vecino = User::factory()->create([
            'email' => 'suspendido@test.local',
            'password' => 'clave12345',
        ]);
        $vecino->createToken('api');

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/usuarios/{$vecino->id}", ['activo' => false])
            ->assertOk()
            ->assertJsonPath('data.activo', false);

        $this->assertDatabaseHas('users', ['id' => $vecino->id, 'activo' => false]);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->postJson('/api/auth/login', [
            'email' => 'suspendido@test.local',
            'password' => 'clave12345',
        ])->assertStatus(422);
    }

    public function test_resetear_la_password_cierra_las_sesiones(): void
    {
        $admin = User::factory()->admin()->create();
        $vecino = User::factory()->create([
            'email' => 'reset@test.local',
            'password' => 'vieja12345',
        ]);
        $vecino->createToken('api');

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/admin/usuarios/{$vecino->id}/password", ['password' => 'nueva12345'])
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->postJson('/api/auth/login', [
            'email' => 'reset@test.local',
            'password' => 'nueva12345',
        ])->assertOk();
    }

    public function test_el_admin_elimina_una_cuenta(): void
    {
        $admin = User::factory()->admin()->create();
        $vecino = User::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/usuarios/{$vecino->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $vecino->id]);
    }

    public function test_el_admin_no_puede_suspenderse_ni_eliminarse_a_si_mismo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/usuarios/{$admin->id}", ['activo' => false])
            ->assertStatus(422);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/usuarios/{$admin->id}")
            ->assertStatus(422);
    }

    public function test_el_resumen_devuelve_los_totales(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['activo' => false]);
        $admin->comercios()->create(['nombre' => 'Almacén']);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/resumen')
            ->assertOk()
            ->assertJsonPath('usuarios.total', 2)
            ->assertJsonPath('usuarios.suspendidos', 1)
            ->assertJsonPath('publicaciones.comercios', 1);
    }
}
