<?php

namespace Database\Factories;

use App\Domain\Usuarios\Roles;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password', // el cast 'hashed' lo encripta al guardar
            'rol' => Roles::VECINO,
            'zona' => fake()->randomElement(['Centro', 'Norte', 'Sur', 'Ruta 301']),
            'activo' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['rol' => Roles::ADMIN]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
