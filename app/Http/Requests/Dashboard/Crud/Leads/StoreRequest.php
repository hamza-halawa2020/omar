<?php

namespace App\Http\Requests\Dashboard\Crud\Leads;

use App\Enums\Leads\FlagType;
use App\Enums\Leads\SourceType;
use App\Enums\Leads\StatusType;
use App\Enums\Leads\TypeOfContact;
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
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'ad_code' => 'required|string|max:255',
            'type_of_contact' => ['nullable', 'string', new Enum(TypeOfContact::class)],
            'source' => ['required', 'string', new Enum(SourceType::class)],
            'assigned_to' => 'required|exists:users,id',
            'creator_id' => 'required|exists:users,id',
            'program_type_id' => 'nullable|exists:crm_program_types,id',
            'notes' => 'nullable|string',
            'flag' => ['nullable', new Enum(FlagType::class)],
            'last_follow_up' => 'nullable|date|before:now',
            'next_follow_up' => 'nullable|date|after:now',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}
