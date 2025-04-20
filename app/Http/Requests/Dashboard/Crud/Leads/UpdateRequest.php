<?php

namespace App\Http\Requests\Dashboard\Crud\Leads;

use App\Enums\Leads\FlagType;
use App\Enums\Leads\SourceType;
use App\Enums\Leads\StatusType;
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
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'source' => ['required', 'string', new Enum(SourceType::class)],
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
            'flag' => ['required', new Enum(FlagType::class)],
            'last_follow_up' => 'date|before:now',
            'next_follow_up' => 'date|after:now',
        ];
    }
}
