<?php

namespace App\Domain\Reclamos\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reclamo extends Model
{
    public const ESTADOS = ['pendiente', 'en_revision', 'en_proceso', 'resuelto'];

    protected $fillable = [
        'user_id', 'categoria', 'descripcion', 'foto_url', 'zona', 'lat', 'lng', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
