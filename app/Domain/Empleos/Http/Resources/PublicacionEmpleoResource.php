<?php

namespace App\Domain\Empleos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Empleos\Models\PublicacionEmpleo */
class PublicacionEmpleoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'experiencia' => $this->experiencia,
            'habilidades' => $this->habilidades,
            'requisitos' => $this->requisitos,
            'disponibilidad' => $this->disponibilidad,
            'horario' => $this->horario,
            'zona' => $this->zona,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'activo' => $this->activo ?? true,
        ];
    }
}
