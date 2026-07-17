<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function manage(User $user, Booking $booking): bool
    {
        return $booking->business->user_id === $user->id;
    }
}
