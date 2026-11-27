<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreChildCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'integer'],
            'weight_kg' => ['required', 'numeric', 'min:1'],
            'height_cm' => ['required', 'numeric', 'min:30'],
            'illness_count' => ['sometimes', 'required', 'integer', 'between:0,4'],
            'focus_level' => ['sometimes', 'required', 'in:much-better,slightly-better,same,slightly-worse,much-worse'],
            'energy_level' => ['sometimes', 'required', 'in:very-high,high,same,low,very-low'],
            'life_events' => ['sometimes', 'required', 'array', 'min:1'],
            'life_events.*' => ['required', 'in:nothing,sport,exams,illness,school,travel,birthday'],
            'food_changes' => ['nullable', 'string', 'max:2000'],
            'body_fat' => ['nullable', 'numeric'],
            'waist_inches' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
