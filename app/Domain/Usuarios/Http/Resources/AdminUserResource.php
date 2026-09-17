<?php

namespace App\Domain\Usuarios\Http\Resources;

use App\Domain\Comercios\Http\Resources\ComercioResource;
use App\Domain\Empleos\Http\Resources\PublicacionEmpleoResource;
use App\Domain\Productores\Http\Resources\ProductorResource;
use App\Domain\Reclamos\Http\Resources\ReclamoResource;
use App\Domain\Resenas\Http\Resources\ResenaResource;
use App\Domain\Servicios\Http\Resources\ServicioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class AdminUserResource extends JsonResource
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
            'activo' => (bool) $this->activo,
            'created_at' => $this->created_at?->toIso8601String(),
            'ultimo_acceso_at' => $this->ultimo_acceso_at?->toIso8601String(),
            'ultima_ip' => $this->ultima_ip,
            'publicaciones' => [
                'servicios' => (int) ($this->servicios_count ?? 0),
                'comercios' => (int) ($this->comercios_count ?? 0),
                'productores' => (int) ($this->productores_count ?? 0),
                'empleos' => (int) ($this->empleos_count ?? 0),
                'reclamos' => (int) ($this->reclamos_count ?? 0),
                'resenas' => (int) ($this->resenas_count ?? 0),
            ],
            'servicios' => $this->whenLoaded('servicios', fn () => ServicioResource::collection($this->servicios)->resolve($request)),
            'comercios' => $this->whenLoaded('comercios', fn () => ComercioResource::collection($this->comercios)->resolve($request)),
            'productores' => $this->whenLoaded('productores', fn () => ProductorResource::collection($this->productores)->resolve($request)),
            'empleos' => $this->whenLoaded('empleos', fn () => PublicacionEmpleoResource::collection($this->empleos)->resolve($request)),
            'reclamos' => $this->whenLoaded('reclamos', fn () => ReclamoResource::collection($this->reclamos)->resolve($request)),
            'resenas' => $this->whenLoaded('resenas', fn () => ResenaResource::collection($this->resenas)->resolve($request)),
        ];
    }
}
