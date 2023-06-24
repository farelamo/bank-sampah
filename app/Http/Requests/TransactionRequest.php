<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start' => 'required|date|date_format:Y-m-d',
            'end' => 'required|date|after_or_equal:start',
        ];
    }

    public function messages(): array
    {
        return [
            'start.required' => 'start date must be filled',
            'start.date' => 'invalid start date',
            'start.date_format' => 'invalid start date format',
            'end.required' => 'end date must be filled',
            'end.date_format' => 'invalid end date format',
            'end.after_or_equal' => 'end date must be greater than or equal with start date ',
            'end.date' => 'invalid end date',
        ];
    }
}
