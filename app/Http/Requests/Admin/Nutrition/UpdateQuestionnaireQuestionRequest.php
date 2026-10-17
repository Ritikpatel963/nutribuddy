<?php

namespace App\Http\Requests\Admin\Nutrition;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionnaireQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage diet chart') ?? false;
    }

    public function rules(): array
    {
        return ['question' => ['required', 'string'], 'sort_order' => ['required', 'integer']];
    }
}
