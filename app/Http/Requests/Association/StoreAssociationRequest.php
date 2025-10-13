<?php

namespace App\Http\Requests\Association;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'per_day' => 'required',
            'monthly_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'total_members' => 'required|array|min:1',
            'total_members.*' => 'exists:clients,id',
        ];
    }
}
