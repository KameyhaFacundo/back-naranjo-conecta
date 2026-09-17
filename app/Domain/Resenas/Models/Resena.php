<?php

namespace App\Domain\Resenas\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Reseña (estrellas + comentario) que un vecino deja sobre un comercio,
 * un servicio o un productor. Una sola por usuario y publicación.
 *
 * @property int $id
 * @property int $user_id
 * @property string $resenable_type
 * @property int $resenable_id
 * @property int $puntuacion
 * @property string|null $comentario
 */
class Resena extends Model
{
    protected $fillable = [
        'user_id', 'resenable_type', 'resenable_id', 'puntuacion', 'comentario',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'resenable_id' => 'integer',
            'puntuacion' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resenable(): MorphTo
    {
        return $this->morphTo();
    }
}
