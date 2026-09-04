<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'country_code' => 'required_with:phone_number|nullable|string|max:8|regex:/^\+[0-9]{1,4}$/',
            'debt' => 'nullable|numeric|min:0',
            'type' => 'nullable'

        ];
    }
}
