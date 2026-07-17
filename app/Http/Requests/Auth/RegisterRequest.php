<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'business_name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(config('nexo.categories'))],
            'city' => ['required', 'string', 'max:120'],
            'whatsapp_phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
