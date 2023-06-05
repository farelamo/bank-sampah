<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|max:100',
            'phone' => 'required|integer',
            'address' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'name must be filled',
            'name.max' => 'maximal name is 100 character',
            'phone.required' => 'phone must be filled',
            'phone.integer' => 'phone must be integer',
            'address.max' => 'address must be filled',
        ];
    }
}
