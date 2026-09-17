<?php

namespace App\Domain\Productores\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Productores\Models\Productor */
class ProductorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'que_produce' => $this->que_produce,
            'que_vende' => $this->que_vende,
            'disponibilidad' => $this->disponibilidad,
            'zona' => $this->zona,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'activo' => $this->activo ?? true,
            'calificacion' => $this->calificacion(),
        ];
    }
}
