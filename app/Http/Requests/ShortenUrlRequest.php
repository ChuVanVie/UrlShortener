<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShortenUrlRequest extends FormRequest
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
            'long_url' => ['long_url' => ['required', 'url', 'max:2048', 'regex:/^https?:\/\//'],],
        ];
    }

    public function messages(): array
    {
        return [
            'long_url.required' => 'The URL field is required.',
            'long_url.url' => 'The URL format is invalid.',
            'long_url.max' => 'The URL must not exceed 2048 characters.',
        ];
    }
}
