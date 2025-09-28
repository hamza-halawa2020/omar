<?php

namespace App\Http\Requests\InstallmentContract;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstallmentContractRequest extends FormRequest
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
            'product_price' => 'sometimes|required|numeric|min:0',
            'down_payment' => 'sometimes|nullable|numeric|min:0',
            'installment_count' => 'sometimes|required|integer|min:1',
            'interest_rate' => 'sometimes|nullable|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'client_id' => 'sometimes|required|exists:clients,id',
            'product_id' => 'sometimes|required|exists:products,id',
        ];
    }
}
