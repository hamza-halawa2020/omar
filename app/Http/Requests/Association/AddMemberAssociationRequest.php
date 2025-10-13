<?php

namespace App\Http\Requests\Association;

use Illuminate\Foundation\Http\FormRequest;

class AddMemberAssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
        ];
    }
}
