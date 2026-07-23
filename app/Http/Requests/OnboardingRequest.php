<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardingRequest extends FormRequest
{
    /**
     * Same business fields as registration (RegisterRequest) — the parts that
     * can't come from OIDC claims.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(config('nexo.categories'))],
            'city' => ['required', 'string', 'max:120'],
            'whatsapp_phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
