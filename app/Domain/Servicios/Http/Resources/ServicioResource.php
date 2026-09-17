<?php

namespace App\Domain\Servicios\Http\Resources;

use App\Domain\Categorias\Http\Resources\CategoriaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Servicios\Models\Servicio */
class ServicioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'subcategoria' => $this->subcategoria,
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'experiencia' => $this->experiencia,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'horarios' => $this->horarios,
            'disponible' => $this->disponible,
            'zona' => $this->zona,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'foto_url' => $this->foto_url,
            'activo' => $this->activo ?? true,
            'distancia_km' => $this->when(isset($this->distancia_km), fn () => round((float) $this->distancia_km, 1)),
            'prestador' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'nombre' => $this->user->nombre,
            ]),
            'calificacion' => $this->calificacion(),
        ];
    }
}
