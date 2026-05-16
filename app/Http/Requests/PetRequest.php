<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:available,pending,sold'],
            'category_name' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'photo_urls' => ['nullable', 'array'],
            'photo_urls.*' => ['url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be: available, pending or sold.',
            'photo_urls.*.url' => 'Photo must be a valid URL.',
            'tags.*.string' => 'Each tag must be a string.',
        ];
    }
}
