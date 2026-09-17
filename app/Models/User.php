<?php

namespace App\Models;

use App\Domain\Avisos\Models\Aviso;
use App\Domain\Comercios\Models\Comercio;
use App\Domain\Empleos\Models\PublicacionEmpleo;
use App\Domain\Productores\Models\Productor;
use App\Domain\Reclamos\Models\Reclamo;
use App\Domain\Resenas\Models\Resena;
use App\Domain\Servicios\Models\Servicio;
use App\Notifications\ResetPasswordNotification;
// Illuminate\Foundation\Auth\User genera automáticamente el getAuthPassword(),
// remember_token, etc. requeridos por el guard de sesión/Sanctum.
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Usuario de la plataforma. El "rol" determina qué puede hacer
 * (ver app/Domain/Usuarios/Roles.php) — un mismo vecino puede además
 * publicar un servicio, un comercio, etc. sin cambiar de rol base.
 *
 * @property int $id
 * @property string $nombre
 * @property string $email
 * @property string $rol
 * @property string|null $telefono
 * @property string|null $whatsapp
 * @property string|null $zona
 * @property float|null $lat
 * @property float|null $lng
 * @property bool $activo
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol',
        'telefono',
        'whatsapp',
        'zona',
        'lat',
        'lng',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
            'activo' => 'boolean',
            'ultimo_acceso_at' => 'datetime',
        ];
    }

    protected static function newFactory(): \Database\Factories\UserFactory
    {
        return \Database\Factories\UserFactory::new();
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }

    public function comercios(): HasMany
    {
        return $this->hasMany(Comercio::class);
    }

    public function productores(): HasMany
    {
        return $this->hasMany(Productor::class);
    }

    public function empleos(): HasMany
    {
        return $this->hasMany(PublicacionEmpleo::class);
    }

    public function reclamos(): HasMany
    {
        return $this->hasMany(Reclamo::class);
    }

    public function avisos(): HasMany
    {
        return $this->hasMany(Aviso::class);
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }
}
