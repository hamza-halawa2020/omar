<?php

namespace App\Http\Requests\Api\Leads;

use App\Enums\Leads\FlagType;
use App\Enums\Leads\SourceType;
use App\Http\Requests\Dashboard\CrmAdminRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends CrmAdminRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:2|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'source' => ['nullable', 'string', new Enum(SourceType::class)],
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'flag' => ['nullable', new Enum(FlagType::class)],
            'last_follow_up' => 'nullable|date|before:now',
            'next_follow_up' => 'nullable|date|after:now',
            'status_id' => 'nullable|integer|exists:crm_leads_statuses,id',
            'status_reason' => 'nullable|string|max:255',
        ];
    }
}
