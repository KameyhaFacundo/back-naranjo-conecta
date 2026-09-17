<?php

namespace App\Domain\Comercios\Models;

use App\Domain\Categorias\Models\Categoria;
use App\Domain\Resenas\Concerns\ConResenas;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $categoria_id
 * @property string $nombre
 */
class Comercio extends Model
{
    use ConResenas;

    protected $fillable = [
        'user_id', 'categoria_id', 'nombre', 'descripcion', 'direccion',
        'horarios', 'telefono', 'whatsapp', 'redes_sociales', 'zona',
        'lat', 'lng', 'logo_url', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'redes_sociales' => 'array',
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
