<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'body' => 'required|string|min:3',
            'is_sale_offer' => 'required|boolean',
            'price' => 'nullable|numeric|required_if:is_sale_offer,true',
            'tag_ids' => 'array|required',
            'genre_ids' => 'array|required',
        ];
    }
}
