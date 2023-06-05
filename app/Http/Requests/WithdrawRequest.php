<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:save,transfer,manual',
            'bank_name' => 'required_if:type,save,transfer|max:100',
            'bank_number' => 'required_if:type,save,transfer|max:100',
            'wallet_number' => 'required_if:type,save,transfer|max:100',
            'cash_out' => 'required_if:type,save,transfer|integer',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'type must be filled',
            'type.in' => 'type doesnt exists',
            'bank_name.required_if' => 'bank name must be filled',
            'bank_name.max' => 'maximal bank name is 100 character',
            'bank_number.required_if' => 'bank number must be filled',
            'bank_number.max' => 'maximal bank number is 100 character',
            'wallet_number.required_if' => 'wallet number must be filled',
            'wallet_number.max' => 'maximal wallet number is 100 character',
            'cash_out.required_if' => 'cash out must be filled',
            'cash_out.integer' => 'invalid cash out type',
        ];
    }
}
