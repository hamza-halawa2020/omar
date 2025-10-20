<?php

namespace App\Http\Requests\Association;

use Illuminate\Foundation\Http\FormRequest;

class addPaymentAssociationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => 'required|exists:association_members,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required|in:paid,pending,late',
            'payment_way_id' => 'required'
        ];
    }
}
