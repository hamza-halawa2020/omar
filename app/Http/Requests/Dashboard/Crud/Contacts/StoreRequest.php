<?php

namespace App\Http\Requests\Dashboard\Crud\Contacts;

use App\Http\Requests\Dashboard\CrmAdminRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends CrmAdminRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'account_id' => 'required|exists:crm_accounts,id',
        ];
    }
}
