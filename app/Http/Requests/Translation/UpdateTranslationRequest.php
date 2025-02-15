<?php

namespace App\Http\Requests\Translation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTranslationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id'); // Get ID from route (for updates)

        return [
            'locale' => 'sometimes|string',
            'key' => [
                'sometimes',
                'string',
                Rule::unique('translations')->where(fn ($query) => $query->where('locale', $this->locale)
                )->ignore($id), // Ignore existing record when updating
            ],
            'content' => 'sometimes|string',
            'tags' => 'sometimes|array',
        ];
    }
}
