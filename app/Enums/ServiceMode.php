<?php

namespace App\Enums;

enum ServiceMode: string
{
    case InPerson = 'in_person';
    case Virtual = 'virtual';

    public function label(): string
    {
        return match ($this) {
            self::InPerson => __('In person'),
            self::Virtual => __('Virtual'),
        };
    }
}
