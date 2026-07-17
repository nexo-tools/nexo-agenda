<?php

namespace App\Http\Requests;

use App\Models\FeedbackReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedbackRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(FeedbackReport::TYPES)],
            'message' => ['required', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:255'],
            'page_url' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'type' => __('tipo'),
            'message' => __('mensaje'),
            'email' => __('email'),
        ];
    }
}
