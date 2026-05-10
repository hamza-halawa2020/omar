<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id') ?? $this->route('role');
        if (is_object($roleId) && method_exists($roleId, 'getKey')) {
            $roleId = $roleId->getKey();
        }

        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'permissions' => 'sometimes|array',
        ];
    }
}
