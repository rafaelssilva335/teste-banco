<?php

namespace Infrastructure\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class FormRequest extends Request
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
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Validate the request.
     * 
     * @throws ValidationException
     */
    public function validate(): array
    {
        // No Lumen, o validator está disponível via app('validator')
        $factory = app('validator');
        $validator = $factory->make(
            $this->all(),
            $this->rules(),
            $this->messages()
        );

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        return $validator->validated();
    }

    /**
     * Handle a failed validation attempt.
     * 
     * Permite customização do comportamento quando validação falha.
     * Por padrão, lança ValidationException que será capturada pelo ExceptionHandler.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new ValidationException($validator);
    }

    /**
     * Get validated data
     */
    public function validated(): array
    {
        return $this->validate();
    }
}
