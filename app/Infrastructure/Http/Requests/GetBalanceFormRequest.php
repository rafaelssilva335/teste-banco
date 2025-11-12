<?php

namespace Infrastructure\Http\Requests;

class GetBalanceFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Account ID is required',
        ];
    }
}
