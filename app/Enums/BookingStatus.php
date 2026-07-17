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
            self::Confirmed => __('Confirmado'),
            self::Cancelled => __('Cancelado'),
            self::Attended => __('Asistió'),
            self::NoShow => __('No asistió'),
        };
    }

    public function occupiesSlot(): bool
    {
        return $this !== self::Cancelled;
    }
}
