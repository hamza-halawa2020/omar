<?php

namespace App\Http\Requests\Iphone;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIphoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_type' => 'sometimes|required|string|max:255',
            'device_details' => 'nullable|string|max:1000',
            'purchase_price_sar' => 'nullable|numeric|min:0',
            'currency' => 'sometimes|required|string|max:20',
            'purchase_price_egp' => 'sometimes|required|numeric|min:0',
            'extra_expenses' => 'nullable|numeric|min:0',
            'sale_price_egp' => 'nullable|numeric|min:0',
        ];
    }
}
