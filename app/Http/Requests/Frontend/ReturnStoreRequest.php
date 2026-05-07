<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ReturnStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'comments' => ['nullable', 'string', 'max:1000'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,mp4,mov', 'max:10240'],
        ];
    }
}
