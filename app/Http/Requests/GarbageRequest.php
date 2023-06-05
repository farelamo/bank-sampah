<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GarbageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'price' => 'required',
            'unit' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'name must be filled',
            'price.required' => 'price must be filled',
            'unit.required' => 'unit must be filled',
        ];
    }
}
