<?php

namespace Database\Seeders;

use App\Domain\Usuarios\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategoriaSeeder::class);

        User::firstOrCreate(
            ['email' => 'admin@elnaranjoconecta.local'],
            [
                'nombre' => 'Administrador',
                'password' => 'cambiar-esta-clave',
                'rol' => Roles::ADMIN,
            ]
        );

        User::firstOrCreate(
            ['email' => 'kameyhafacundo@gmail.com'],
            [
                'nombre' => 'Facundo Kameyha',
                'password' => 'naranjo2026',
                'rol' => Roles::ADMIN,
            ]
        );

        // Datos de ejemplo solo en local, para no ensuciar producción.
        if (app()->environment('local')) {
            $this->call(DemoSeeder::class);
        }
    }
}
