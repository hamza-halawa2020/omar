<?php

namespace App\Http\Requests\Association;

use Illuminate\Foundation\Http\FormRequest;

class payMemberAssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_way_id' => 'required|exists:payment_ways,id',
            'member_id' => 'required|exists:association_members,id',
            'commission' => 'nullable|numeric|min:0',
        ];
    }
}
