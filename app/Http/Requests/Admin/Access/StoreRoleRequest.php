<?php

namespace App\Http\Requests\Admin\Access;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage roles') ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'unique:roles,name'], 'permissions' => ['array']];
    }
}
