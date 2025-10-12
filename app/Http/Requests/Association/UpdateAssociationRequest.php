<?php

namespace App\Http\Requests\Association;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'total_members' => 'sometimes|required|integer|min:1',
            'monthly_amount' => 'sometimes|required|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|required|in:active,completed,paused',
        ];
    }
}
