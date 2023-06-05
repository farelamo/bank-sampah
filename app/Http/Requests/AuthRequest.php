<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'username must be filled',
            'password.required' => 'password must be filled',
        ];
    }
}
