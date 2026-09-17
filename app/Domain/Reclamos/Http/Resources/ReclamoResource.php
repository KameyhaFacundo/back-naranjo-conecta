<?php

namespace App\Domain\Reclamos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Reclamos\Models\Reclamo */
class ReclamoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoria' => $this->categoria,
            'descripcion' => $this->descripcion,
            'foto_url' => $this->foto_url,
            'zona' => $this->zona,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'estado' => $this->estado,
            'creado' => $this->created_at?->toISOString(),
        ];
    }
}
