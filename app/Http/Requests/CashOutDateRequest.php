<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashOutDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date|date_format:Y-m-d'
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'date must be filled',
            'date.date' => 'invalid date',
            'date.date_format' => 'invalid date format',
        ];
    }
}
