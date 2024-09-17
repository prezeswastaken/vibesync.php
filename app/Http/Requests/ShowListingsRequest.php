<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowListingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'currency_id' => 'nullable|integer|exists:currencies,id',
        ];
    }
}
