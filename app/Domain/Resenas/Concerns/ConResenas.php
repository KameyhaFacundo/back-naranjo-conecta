<?php

namespace App\Domain\Resenas\Concerns;

use App\Domain\Resenas\Models\Resena;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Se usa en los modelos calificables (Comercio, Servicio, Productor) para
 * exponer sus reseñas y el promedio de estrellas.
 */
trait ConResenas
{
    public function resenas(): MorphMany
    {
        return $this->morphMany(Resena::class, 'resenable');
    }

    /**
     * Promedio + cantidad de reseñas. Aprovecha los agregados cargados con
     * withAvg()/withCount() (evita N+1 en los listados) y, si no están,
     * los calcula con una consulta.
     */
    public function calificacion(): array
    {
        if (array_key_exists('resenas_promedio', $this->attributes)) {
            $promedio = $this->attributes['resenas_promedio'];
            $total = $this->attributes['resenas_count'] ?? 0;
        } else {
            $promedio = $this->resenas()->avg('puntuacion');
            $total = $this->resenas()->count();
        }

        return [
            'promedio' => $promedio !== null ? round((float) $promedio, 1) : null,
            'total' => (int) $total,
        ];
    }
}
