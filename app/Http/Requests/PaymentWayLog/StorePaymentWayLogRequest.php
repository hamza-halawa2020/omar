<?php

namespace App\Http\Requests\PaymentWayLog;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentWayLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_way_id' => 'required|exists:payment_ways,id',
            'action'         => 'required|in:create,update,delete',
            'data'           => 'nullable|array',
        ];
    }
}
