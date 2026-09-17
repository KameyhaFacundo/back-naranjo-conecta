<?php

namespace App\Domain\Resenas\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Resenas\Models\Resena */
class ResenaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'puntuacion' => $this->puntuacion,
            'comentario' => $this->comentario,
            'autor' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'nombre' => $this->user->nombre,
            ]),
            'es_mia' => $request->user() !== null && (int) $request->user()->id === (int) $this->user_id,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
