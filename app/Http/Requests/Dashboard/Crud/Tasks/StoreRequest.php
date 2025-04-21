<?php

namespace App\Http\Requests\Dashboard\Crud\Tasks;

use App\Enums\Tasks\RelatedToType;
use App\Enums\Tasks\Status;
use App\Http\Requests\Dashboard\CrmAdminRequest;
use App\Models\CrmModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreRequest extends CrmAdminRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert the due_date to Africa/Cairo timezone
        if ($this->has('due_date')) {
            $this->merge([
                'due_date' => Carbon::parse($this->due_date)->timezone('Africa/Cairo')
            ]);
        }
    }

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
}
