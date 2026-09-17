<?php

namespace App\Domain\Productores\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductorRequest extends FormRequest
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
            'que_produce' => ['nullable', 'string'],
            'que_vende' => ['nullable', 'string'],
            'disponibilidad' => ['nullable', 'string', 'max:255'],
            'zona' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'activo' => ['boolean'],
        ];
    }
}
