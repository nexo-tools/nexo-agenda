<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Attended = 'attended';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Confirmed => __('Confirmed'),
            self::Cancelled => __('Cancelled'),
            self::Attended => __('Attended'),
            self::NoShow => __('No-show'),
        };
    }

    public function occupiesSlot(): bool
    {
        return $this !== self::Cancelled;
    }
}
