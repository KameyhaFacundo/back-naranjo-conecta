<?php

namespace App\Domain\Instituciones\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Instituciones\Models\Institucion */
class InstitucionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'horarios' => $this->horarios,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
