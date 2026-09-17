<?php

namespace App\Domain\Empleos\Models;

use App\Domain\Categorias\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Publicación de "Busco trabajo" o "Busco trabajador" (campo tipo).
 */
class PublicacionEmpleo extends Model
{
    protected $table = 'empleos';

    public const TIPO_BUSCO_TRABAJO = 'busco_trabajo';

    public const TIPO_BUSCO_TRABAJADOR = 'busco_trabajador';

    protected $fillable = [
        'user_id', 'tipo', 'titulo', 'categoria_id', 'descripcion', 'experiencia',
        'habilidades', 'requisitos', 'disponibilidad', 'horario', 'zona',
        'telefono', 'whatsapp', 'lat', 'lng', 'activo',
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
