<?php

namespace App\Http\Requests;

use App\Enums\ServiceMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'mode' => ['required', Rule::enum(ServiceMode::class)],
            'video_link' => ['nullable', 'required_if:mode,virtual', 'url:https', 'max:255'],
            'buffer_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'min_notice_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'cancellation_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'max_advance_days' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['boolean'],
        ];
    }

    /** @return array<string, mixed> */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (is_array($data)) {
            $data['is_active'] = $this->boolean('is_active');

            if (($data['mode'] ?? null) === ServiceMode::InPerson->value) {
                $data['video_link'] = null;
            }
        }

        return $data;
    }
}
