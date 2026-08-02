<?php

namespace App\Services;

use App\Mail\WaitlistSlotFreed;
use App\Models\Booking;
use App\Models\WaitlistEntry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;

class WaitlistNotifier
{
    /**
     * Tell everyone waiting for this booking's day (and service) that a slot
     * just freed up. Each entry is notified at most once.
     */
    public function bookingCancelled(Booking $booking): void
    {
        $booking->loadMissing(['business', 'service', 'professional']);

        $localDate = $booking->starts_at->setTimezone($booking->business->timezone)->toDateString();

        if ($localDate < CarbonImmutable::now($booking->business->timezone)->toDateString()) {
            return;
        }

        $entries = WaitlistEntry::query()
            ->where('business_id', $booking->business_id)
            ->where('service_id', $booking->service_id)
            ->whereDate('date', $localDate)
            ->whereNull('notified_at')
            ->where(fn ($query) => $query
                ->whereNull('professional_id')
                ->orWhere('professional_id', $booking->professional_id))
            ->get();

        foreach ($entries as $entry) {
            // The waitlist entry has no locale of its own, and the person who
            // cancelled is somebody else entirely: the instance language is the
            // only honest default here.
            Mail::to($entry->client_email)
                ->locale(config('app.locale'))
                ->queue(new WaitlistSlotFreed($entry, $booking));
            $entry->forceFill(['notified_at' => CarbonImmutable::now()])->save();
        }
    }
}
