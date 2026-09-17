<?php

namespace App\Domain\Avisos\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Avisos\Models\Aviso */
class AvisoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'cuerpo' => $this->cuerpo,
            'foto_url' => $this->foto_url,
            'tipo' => $this->tipo,
            'fecha_evento' => $this->fecha_evento?->toISOString(),
            'publicado' => $this->created_at?->toISOString(),
        ];
    }
}
