<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\View\View;

class BookingManagementController extends Controller
{
    public function show(string $token): View
    {
        $booking = Booking::findByManagementToken($token);

        abort_if($booking === null, 404);

        return view('public.booking.manage', [
            'booking' => $booking->load(['business', 'service', 'professional']),
            'token' => $token,
        ]);
    }
}
