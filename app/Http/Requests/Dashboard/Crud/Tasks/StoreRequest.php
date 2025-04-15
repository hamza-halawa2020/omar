<?php

namespace App\Http\Requests\Dashboard\Crud\Tasks;

use App\Enums\Tasks\RelatedToType;
use App\Enums\Tasks\Status;
use App\Http\Requests\Dashboard\CrmAdminRequest;
use App\Models\CrmModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'title' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'status' => ['required', 'string'],
            'related_to_type' => ['required', 'string'],
            'related_to_id' => 'required|integer',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        $validated['related_to_type'] = RelatedToType::valueFromName($validated['related_to_type']);

        return $validated;
    }
}
