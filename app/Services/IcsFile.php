<?php

namespace App\Services;

use App\Enums\ServiceMode;
use App\Models\Booking;
use App\Models\Professional;

class IcsFile
{
    public function forBooking(Booking $booking): string
    {
        $business = $booking->business;
        $service = $booking->service;

        $location = $service->mode === ServiceMode::Virtual
            ? (string) $service->video_link
            : trim(($business->address ? $business->address.', ' : '').$business->city);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Nexo Agenda//ES',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:booking-'.$booking->id.'@nexoagenda',
            'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:'.$booking->starts_at->utc()->format('Ymd\THis\Z'),
            'DTEND:'.$booking->ends_at->utc()->format('Ymd\THis\Z'),
            'SUMMARY:'.$this->escape($service->name.' — '.$business->name),
            'DESCRIPTION:'.$this->escape(__('Appointment with :professional', ['professional' => $booking->professional->name])),
            'LOCATION:'.$this->escape($location),
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines)."\r\n";
    }

    /**
     * Subscription feed with the professional's upcoming (and recent) bookings.
     */
    public function forProfessional(Professional $professional): string
    {
        $business = $professional->business;

        $bookings = $professional->bookings()
            ->occupying()
            ->where('starts_at', '>=', now()->subDays(30))
            ->with('service:id,name')
            ->orderBy('starts_at')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Nexo Agenda//ES',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:'.$this->escape($professional->name.' — '.$business->name),
            'X-WR-TIMEZONE:'.$business->timezone,
        ];

        foreach ($bookings as $booking) {
            $lines = [
                ...$lines,
                'BEGIN:VEVENT',
                'UID:booking-'.$booking->id.'@nexoagenda',
                'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
                'DTSTART:'.$booking->starts_at->utc()->format('Ymd\THis\Z'),
                'DTEND:'.$booking->ends_at->utc()->format('Ymd\THis\Z'),
                'SUMMARY:'.$this->escape($booking->client_name.' — '.$booking->service->name),
                'STATUS:CONFIRMED',
                'END:VEVENT',
            ];
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    private function escape(string $value): string
    {
        return str_replace([',', ';', "\n"], ['\,', '\;', '\n'], $value);
    }
}
