<?php

namespace App\Domain\Servicios\Models;

use App\Domain\Categorias\Models\Categoria;
use App\Domain\Resenas\Concerns\ConResenas;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ficha de un prestador de servicios (albañil, electricista, etc.).
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $categoria_id
 * @property string $titulo
 * @property string|null $zona
 * @property float|null $lat
 * @property float|null $lng
 */
class Servicio extends Model
{
    use ConResenas;

    protected $fillable = [
        'user_id', 'categoria_id', 'subcategoria', 'titulo', 'descripcion',
        'experiencia', 'telefono', 'whatsapp', 'horarios', 'disponible',
        'zona', 'lat', 'lng', 'foto_url', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
            'activo' => 'boolean',
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
