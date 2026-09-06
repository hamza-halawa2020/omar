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
            'quantity' => 'nullable|integer|min:1',
            'product_id' => 'nullable|exists:products,id',
            'products' => 'nullable|array',
            'products.*.product_id' => 'nullable|distinct|exists:products,id',
            'products.*.quantity' => 'nullable|integer|min:1',
            'payment_way_id' => 'sometimes|nullable|exists:payment_ways,id',
            'type'            => 'required|in:send,receive',
            'amount'          => 'required|numeric|min:0',
            'commission'      => 'required|numeric|min:0',
            'notes'           => 'nullable|string|max:255',
            'attachment'      => 'nullable',
        ];
    }
}
