<?php

namespace Infrastructure\Http\Requests;

class EventFormRequest extends FormRequest
{
    public function rules(): array
    {
        $type = $this->input('type');
        
        // Regras base para todos os eventos
        $rules = [
            'type' => 'required|string|in:deposit,withdraw,transfer',
        ];

        // Regras específicas por tipo
        if ($type === 'deposit') {
            $rules['destination'] = 'required|string';
            $rules['amount'] = 'required|numeric|min:0.01';
        } elseif ($type === 'withdraw') {
            $rules['origin'] = 'required|string';
            $rules['amount'] = 'required|numeric|min:0.01';
        } elseif ($type === 'transfer') {
            $rules['origin'] = 'required|string';
            $rules['destination'] = 'required|string|different:origin';
            $rules['amount'] = 'required|numeric|min:0.01';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Type is required',
            'type.in' => 'Type must be one of: deposit, withdraw, transfer',
            'destination.required' => 'Destination account ID is required',
            'destination.different' => 'Origin and destination must be different',
            'origin.required' => 'Origin account ID is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be greater than 0',
        ];
    }

    public function getType(): string
    {
        return $this->input('type');
    }
}
