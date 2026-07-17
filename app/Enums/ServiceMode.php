<?php

namespace App\Enums;

enum ServiceMode: string
{
    case InPerson = 'in_person';
    case Virtual = 'virtual';

    public function label(): string
    {
        return match ($this) {
            self::InPerson => __('Presencial'),
            self::Virtual => __('Virtual'),
        };
    }
}
