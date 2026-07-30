<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function show(string $token): View
    {
        $booking = $this->findOrAbort($token);

        return view('app.checkin', [
            'booking' => $booking->load(['service', 'professional', 'business']),
            'token' => $token,
        ]);
    }

    public function store(string $token): RedirectResponse
    {
        $booking = $this->findOrAbort($token);

        if ($booking->status === BookingStatus::Confirmed) {
            $booking->update(['status' => BookingStatus::Attended]);
        }

        return redirect()
            ->route('dashboard', ['date' => $booking->starts_at->setTimezone($booking->business->timezone)->toDateString()])
            ->with('status', __(':name marked as Attended.', ['name' => $booking->client_name]));
    }

    private function findOrAbort(string $token): Booking
    {
        $booking = Booking::findByManagementToken($token);

        abort_if($booking === null, 404);

        Gate::authorize('manage', $booking);

        return $booking;
    }
}
