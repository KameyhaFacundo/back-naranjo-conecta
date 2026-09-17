<?php

namespace App\Domain\Comercios\Http\Resources;

use App\Domain\Categorias\Http\Resources\CategoriaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Comercios\Models\Comercio */
class ComercioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'direccion' => $this->direccion,
            'horarios' => $this->horarios,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'redes_sociales' => $this->redes_sociales,
            'zona' => $this->zona,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'logo_url' => $this->logo_url,
            'activo' => $this->activo ?? true,
            'calificacion' => $this->calificacion(),
        ];
    }
}
