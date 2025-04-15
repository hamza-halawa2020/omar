<?php

namespace App\Http\Requests\Dashboard\Crud\Deals;

use App\Enums\Deals\StageType;
use App\Http\Requests\Dashboard\CrmAdminRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'name' => 'required',
            'amount' => 'required|numeric|min:0',
            'stage' => [new Enum(StageType::class)],
            'contact_id' => 'exists:crm_contacts,id',
            'account_id' => 'required|exists:crm_accounts,id',
            'expected_close_date' => 'nullable|date|after_or_equal:today',
        ];
    }
}
