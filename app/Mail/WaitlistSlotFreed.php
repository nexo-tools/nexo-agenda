<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\WaitlistEntry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WaitlistSlotFreed extends Mailable
{
    public function __construct(
        public readonly WaitlistEntry $entry,
        public readonly Booking $booking,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('¡Se liberó un horario en :business!', ['business' => $this->booking->business->name]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.waitlist-slot-freed');
    }
}
