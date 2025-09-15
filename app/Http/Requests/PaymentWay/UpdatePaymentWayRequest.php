<?php

namespace App\Http\Requests\PaymentWay;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentWayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'     => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'name'         => 'sometimes|required|string|max:255',
            'type'         => 'sometimes|required|in:cash,wallet,balance_machine',
            'phone_number' => 'nullable|string|max:20',
            'send_limit' => 'nullable|numeric|min:0',
            'receive_limit' => 'nullable|numeric|min:0',
        ];
    }
}
