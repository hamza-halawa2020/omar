<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'client_id' => 'nullable',
            'quantity' => 'nullable',
            'product_id' => 'nullable',
            'sometimes|nullable|exists:payment_ways,id',
            'type'            => 'required|in:send,receive',
            'amount'          => 'required|numeric|min:0',
            'commission'      => 'required|numeric|min:0',
            'notes'           => 'nullable|string|max:255',
            'attachment'      => 'nullable',
        ];
    }
}