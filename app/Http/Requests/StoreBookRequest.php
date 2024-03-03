<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'publisher_id' => ['required', 'numeric'],
            'title' => ['required', 'string'],
            'genre' => ['required', 'string'],
            'author' => ['required', 'string'],
            'year' => ['required', 'numeric', 'gt:0'],
            'pages' => ['required', 'numeric', 'gt:0'],
            'language' => ['required', 'string'],
            'edition' => ['required', 'numeric', 'gt:0'],
            'isbn' => ['required', 'string', 'unique:books'],
        ];
    }
}
