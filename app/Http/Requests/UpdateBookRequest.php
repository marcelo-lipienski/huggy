<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'publisher_id' => ['sometimes', 'required', 'numeric'],
            'title' => ['sometimes', 'required', 'string'],
            'genre' => ['sometimes', 'required', 'string'],
            'author' => ['sometimes', 'required', 'string'],
            'year' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'pages' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'language' => ['sometimes', 'required', 'string'],
            'edition' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'isbn' => ['sometimes', 'required', 'string', 'unique:books'],
        ];
    }
}
