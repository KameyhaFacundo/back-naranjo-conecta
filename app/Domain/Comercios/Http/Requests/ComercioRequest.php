<?php

namespace App\Domain\Comercios\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComercioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sometimes = $this->isMethod('PUT') || $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'nombre' => [$sometimes, 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'horarios' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'redes_sociales' => ['nullable', 'array'],
            'zona' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'logo_url' => ['nullable', 'url'],
            'activo' => ['boolean'],
        ];
    }
}
