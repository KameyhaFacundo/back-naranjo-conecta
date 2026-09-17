<?php

namespace App\Domain\Empleos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicacionEmpleoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sometimes = $this->isMethod('PUT') || $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'tipo' => [$sometimes, Rule::in(['busco_trabajo', 'busco_trabajador'])],
            'titulo' => [$sometimes, 'string', 'max:255'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'descripcion' => ['nullable', 'string'],
            'experiencia' => ['nullable', 'string', 'max:255'],
            'habilidades' => ['nullable', 'string'],
            'requisitos' => ['nullable', 'string'],
            'disponibilidad' => ['nullable', 'string', 'max:255'],
            'horario' => ['nullable', 'string', 'max:255'],
            'zona' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'activo' => ['boolean'],
        ];
    }
}
