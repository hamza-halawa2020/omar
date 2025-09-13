<?php

namespace App\Http\Requests\TransactionLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|exists:transactions,id',
            'action'         => 'required|in:create,update,delete',
            'data'           => 'nullable|array',
        ];
    }
}
