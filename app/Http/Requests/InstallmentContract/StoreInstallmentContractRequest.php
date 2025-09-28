<?php

namespace App\Http\Requests\InstallmentContract;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstallmentContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_price' => 'required|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'installment_count' => 'required|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
        ];
    }
}
