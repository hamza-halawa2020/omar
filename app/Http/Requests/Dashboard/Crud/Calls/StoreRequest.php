<?php

namespace App\Http\Requests\Dashboard\Crud\Calls;

use App\Enums\Calls\OutcomeType;
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
            'related_to_type' => 'required|string',
            'related_to_id' => 'required|integer',
            'call_status_id' => 'required',
            'subject' => 'required|string',
            'call_time' => 'required|date',
            'duration_in_minutes' => 'required|integer',
            'notes' => 'nullable|string',
            'outcome' => ['required', 'string', new Enum(OutcomeType::class)],
        ];
    }
}
