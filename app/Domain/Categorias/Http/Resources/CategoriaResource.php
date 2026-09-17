<?php

namespace App\Domain\Categorias\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Domain\Categorias\Models\Categoria */
class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'modulo' => $this->modulo,
            'icono' => $this->icono,
        ];
    }
}
