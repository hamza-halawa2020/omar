<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProfileRequest extends FormRequest
{
    private const ALLOWED_WHATSAPP_TEMPLATE_VARIABLES = [
        ':name',
        ':amount',
        ':product',
        ':association',
        ':balance',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:central.users,email,' . $userId,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:8',
            'whatsapp_api_token' => 'nullable|string',
            'whatsapp_templates' => 'nullable|array',
            'whatsapp_templates.*' => 'nullable|string|max:2000',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach ((array) $this->input('whatsapp_templates', []) as $key => $template) {
                    preg_match_all('/:[\pL\pN_]+/u', (string) $template, $matches);

                    $invalidVariables = array_values(array_diff(array_unique($matches[0] ?? []), self::ALLOWED_WHATSAPP_TEMPLATE_VARIABLES));

                    if ($invalidVariables !== []) {
                        $validator->errors()->add(
                            "whatsapp_templates.{$key}",
                            __('messages.whatsapp_template_invalid_variables', [
                                'variables' => implode(', ', $invalidVariables),
                                'allowed' => implode(', ', self::ALLOWED_WHATSAPP_TEMPLATE_VARIABLES),
                            ])
                        );
                    }
                }
            },
        ];
    }
}
