<?php

namespace App\Domain\Productores\Models;

use App\Domain\Categorias\Models\Categoria;
use App\Domain\Resenas\Concerns\ConResenas;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Productor extends Model
{
    use ConResenas;

    protected $table = 'productores';

    protected $fillable = [
        'user_id', 'categoria_id', 'nombre', 'que_produce', 'que_vende',
        'disponibilidad', 'zona', 'telefono', 'whatsapp', 'lat', 'lng', 'activo',
    ];

    protected function casts(): array
    {
        return [
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
