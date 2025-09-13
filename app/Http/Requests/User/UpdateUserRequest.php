<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $this->route('user'),
            'password' => 'nullable|string|min:8|confirmed',

            'roles'    => 'nullable|array',
            'roles.*'  => 'exists:roles,name',
        ];
    }
}
