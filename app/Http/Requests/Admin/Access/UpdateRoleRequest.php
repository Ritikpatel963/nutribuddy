<?php

namespace App\Http\Requests\Admin\Access;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage roles') ?? false;
    }

    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => ['required', 'string', Rule::unique('roles', 'name')->ignore($role instanceof Role ? $role->id : null)],
            'permissions' => ['array'],
        ];
    }
}
