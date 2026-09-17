<?php

namespace App\Domain\Usuarios\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'rol' => $this->rol,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'zona' => $this->zona,
        ];
    }
}
