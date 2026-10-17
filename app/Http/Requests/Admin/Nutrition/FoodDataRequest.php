<?php

namespace App\Http\Requests\Admin\Nutrition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FoodDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('view diet chart')
            || $this->user('admin')?->can('manage diet chart');
    }

    public function rules(): array
    {
        return [
            'draw' => ['nullable', 'integer', 'min:0'],
            'start' => ['nullable', 'integer', 'min:0'],
            'length' => ['nullable', 'integer', 'min:1', 'max:100'],
            'order.0.column' => ['nullable', 'integer', 'between:0,7'],
            'order.0.dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'search.value' => ['nullable', 'string', 'max:255'],
        ];
    }
}
