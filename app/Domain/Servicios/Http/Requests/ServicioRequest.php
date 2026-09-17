<?php

namespace App\Domain\Servicios\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la autorización de "solo el dueño edita" se hace en el controller
    }

    public function rules(): array
    {
        $sometimes = $this->isMethod('PUT') || $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'subcategoria' => ['nullable', 'string', 'max:255'],
            'titulo' => [$sometimes, 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'experiencia' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'horarios' => ['nullable', 'string', 'max:255'],
            'disponible' => ['boolean'],
            'activo' => ['boolean'],
            'zona' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'foto_url' => ['nullable', 'url'],
        ];
    }
}
