<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo'           => 'required|string|max:20',
            'nombre_completo' => 'required|string|max:100',
            'empresa'        => 'required|string|max:100',
            'fecha_registro' => 'required|date',
            'fecha_vigencia' => 'required|date|after_or_equal:fecha_registro',
            'cargo'          => 'required|string|max:50',
            'estado'         => 'required|string|max:20',
        ];
    }
}
