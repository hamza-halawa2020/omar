<?php

namespace App\Http\Requests\Dashboard\Crud\CallStatus;


use App\Http\Requests\Dashboard\CrmAdminRequest;

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
            'workflow_id' => 'required'
        ];
    }
}
