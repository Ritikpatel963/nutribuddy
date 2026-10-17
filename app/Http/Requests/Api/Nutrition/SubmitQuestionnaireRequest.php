<?php

namespace App\Http\Requests\Api\Nutrition;

use App\Models\Nutrition\QuestionnaireQuestion;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SubmitQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = [
            'child_id' => ['nullable', 'integer', 'exists:children,id'],
            'plan_slug' => ['nullable', 'string', 'max:255'],
            'cycle' => ['nullable', 'string', 'in:monthly,quarterly,annually'],
        ];

        foreach ($this->all() as $key => $value) {
            if (preg_match('/^q_[1-9]\d*$/', (string) $key)) {
                $rules[$key] = ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_scalar($value) && ! is_array($value)) {
                        $fail('The answer must be a scalar value or a list of values.');
                    }

                    if (is_array($value) && collect($value)->contains(fn ($item) => ! is_scalar($item) && $item !== null)) {
                        $fail('The answer list may contain only scalar values.');
                    }
                }];
            }
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $questionIds = collect(array_keys($this->all()))
                ->map(fn ($key) => preg_match('/^q_([1-9]\d*)$/', (string) $key, $matches) ? (int) $matches[1] : null)
                ->filter()
                ->unique();

            $existingIds = QuestionnaireQuestion::whereIn('id', $questionIds)->pluck('id')->all();

            foreach ($questionIds->diff($existingIds) as $missingId) {
                $validator->errors()->add("q_{$missingId}", 'The selected question does not exist.');
            }
        });
    }
}
