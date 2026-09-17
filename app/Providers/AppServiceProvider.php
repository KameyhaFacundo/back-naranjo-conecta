<?php

namespace App\Providers;

use App\Domain\Comercios\Models\Comercio;
use App\Domain\Productores\Models\Productor;
use App\Domain\Servicios\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nombres cortos para las relaciones polimórficas (reseñas).
        Relation::morphMap([
            'user' => User::class,
            'comercio' => Comercio::class,
            'servicio' => Servicio::class,
            'productor' => Productor::class,
        ]);
    }
}
