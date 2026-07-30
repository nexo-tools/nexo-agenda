<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProfessionalRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'blocks' => ['nullable', 'array'],
            'blocks.*' => ['array'],
            'blocks.*.*.start' => ['required', 'date_format:H:i'],
            'blocks.*.*.end' => ['required', 'date_format:H:i', 'after:blocks.*.*.start'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $blocks = $this->input('blocks', []);

            if (! is_array($blocks)) {
                return;
            }

            foreach ($blocks as $weekday => $ranges) {
                if (! in_array((int) $weekday, range(1, 7), true)) {
                    $validator->errors()->add('blocks', __('Invalid weekday.'));

                    continue;
                }

                if (! is_array($ranges)) {
                    continue;
                }

                $sorted = collect($ranges)
                    ->filter(fn ($r) => is_array($r) && isset($r['start'], $r['end']))
                    ->sortBy('start')
                    ->values();

                foreach ($sorted as $i => $range) {
                    $previous = $i > 0 ? $sorted[$i - 1] : null;

                    if ($previous !== null && $range['start'] < $previous['end']) {
                        $validator->errors()->add(
                            "blocks.$weekday",
                            __('Time ranges on the same day cannot overlap.')
                        );

                        break;
                    }
                }
            }
        });
    }

    // Field names live in lang/{locale}/validation.php under 'attributes'.
}
