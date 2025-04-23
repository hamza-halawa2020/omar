<?php

namespace App\Http\Requests\Dashboard\Crud\WorkFlow;


use App\Enums\WorkFlow\WorkFlowType;
use App\Http\Requests\Dashboard\CrmAdminRequest;
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
            'name' => 'required|string|min:2|max:255',
            'type' => ['nullable', 'string', new Enum(WorkFlowType::class)],
        ];
    }
}
